<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/test-mongodb-connection', function () {
    try {
        // Test MongoDB connection using Laravel's database facade
        DB::connection('mongodb')->getMongoClient()->listDatabases();
        
        return response()->json([
            'status' => 'success',
            'message' => 'MongoDB connection successful!',
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'MongoDB connection failed: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});
