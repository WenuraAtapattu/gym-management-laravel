<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\MongoLogger;

// Test database connections
Route::get('/test-db', function () {
    // Test MySQL connection
    try {
        DB::connection('mysql')->getPdo();
        $mysql = 'MySQL connection successful!';
    } catch (\Exception $e) {
        $mysql = 'MySQL connection failed: ' . $e->getMessage();
    }

    // Test MongoDB connection
    try {
        DB::connection('mongodb')->getMongoClient()->listDatabases();
        $mongo = 'MongoDB connection successful!';
        // Log a test message to MongoDB
        MongoLogger::info('Test log message', ['test' => true]);
    } catch (\Exception $e) {
        $mongo = 'MongoDB connection failed: ' . $e->getMessage();
    }

    return response()->json(compact('mysql', 'mongo'));
});

// Home route
Route::get('/', function () {
    return response()->view('welcome', [
        'message' => 'Welcome to the Gym Management System'
    ]);
});

// Include explorer routes
require __DIR__.'/explorer.php';

// Include test routes
if (app()->environment('local')) {
    require __DIR__.'/test-data-explorer.php';
    require __DIR__.'/mongodb-test-route.php';
}
