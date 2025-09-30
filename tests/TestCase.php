<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up MongoDB for testing if needed
        if (config('database.default') === 'mongodb') {
            $this->setUpMongoDB();
        }
    }
    
    /**
     * Set up MongoDB for testing.
     */
    protected function setUpMongoDB(): void
    {
        // Clear all collections before each test
        $this->clearMongoDBCollections();
    }
    
    /**
     * Clear all MongoDB collections.
     */
    protected function clearMongoDBCollections(): void
    {
        $collections = DB::connection('mongodb')
            ->listCollections();
            
        foreach ($collections as $collection) {
            $collectionName = $collection->getName();
            
            // Skip system collections
            if (strpos($collectionName, 'system.') === 0) {
                continue;
            }
            
            DB::connection('mongodb')
                ->collection($collectionName)
                ->delete();
        }
    }
    
    /**
     * Run a specific seeder.
     */
    protected function seedMongoDB(string $seederClass): void
    {
        Artisan::call('db:seed', ['--class' => $seederClass]);
    }
}
