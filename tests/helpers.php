<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('mongodb_collection')) {
    /**
     * Get a MongoDB collection instance.
     */
    function mongodb_collection(string $collection)
    {
        return DB::connection('mongodb')->collection($collection);
    }
}

if (!function_exists('mongodb_drop_collections')) {
    /**
     * Drop all collections except those in the except array.
     */
    function mongodb_drop_collections(array $except = []): void
    {
        $collections = DB::connection('mongodb')
            ->listCollections();
            
        foreach ($collections as $collection) {
            $collectionName = $collection->getName();
            
            // Skip system collections and excepted collections
            if (strpos($collectionName, 'system.') === 0 || in_array($collectionName, $except)) {
                continue;
            }
            
            DB::connection('mongodb')
                ->collection($collectionName)
                ->drop();
        }
    }
}

if (!function_exists('mongodb_clear_collections')) {
    /**
     * Clear all collections except those in the except array.
     */
    function mongodb_clear_collections(array $except = []): void
    {
        $collections = DB::connection('mongodb')
            ->listCollections();
            
        foreach ($collections as $collection) {
            $collectionName = $collection->getName();
            
            // Skip system collections and excepted collections
            if (strpos($collectionName, 'system.') === 0 || in_array($collectionName, $except)) {
                continue;
            }
            
            DB::connection('mongodb')
                ->collection($collectionName)
                ->deleteMany([]);
        }
    }
}
