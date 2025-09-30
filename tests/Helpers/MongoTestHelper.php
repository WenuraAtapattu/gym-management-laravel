<?php

namespace Tests\Helpers;

use Illuminate\Support\Facades\DB;

trait MongoTestHelper
{
    /**
     * Assert that a MongoDB collection has the given count.
     */
    protected function assertMongoDBCount(string $collection, int $expectedCount, array $where = []): void
    {
        $count = DB::connection('mongodb')
            ->collection($collection)
            ->when(!empty($where), function ($query) use ($where) {
                return $query->where($where);
            })
            ->count();
            
        $this->assertEquals($expectedCount, $count, "Failed asserting that the collection [{$collection}] has [{$expectedCount}] records.");
    }
    
    /**
     * Assert that a MongoDB collection has the given record.
     */
    protected function assertMongoDBHas(string $collection, array $data): void
    {
        $exists = DB::connection('mongodb')
            ->collection($collection)
            ->where($data)
            ->exists();
            
        $this->assertTrue($exists, "Failed to find record in collection [{$collection}] with the given attributes.");
    }
    
    /**
     * Assert that a MongoDB collection does not have the given record.
     */
    protected function assertMongoDBMissing(string $collection, array $data): void
    {
        $exists = DB::connection('mongodb')
            ->collection($collection)
            ->where($data)
            ->exists();
            
        $this->assertFalse($exists, "Found unexpected record in collection [{$collection}] with the given attributes.");
    }
}
