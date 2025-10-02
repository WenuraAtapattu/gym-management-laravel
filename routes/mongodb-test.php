<?php

use Illuminate\Support\Facades\Route;
use MongoDB\Client as MongoClient;

Route::get('/mongodb-test', function () {
    try {
        // Test MongoDB connection using the MongoDB PHP driver
        $client = new MongoClient(env('MONGODB_URI', 'mongodb://localhost:27017'));
        $databases = $client->listDatabases();
        
        $dbNames = [];
        foreach ($databases as $database) {
            $dbNames[] = $database->getName();
        }
        
        return response()->json([
            'status' => 'success',
            'databases' => $dbNames,
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});
