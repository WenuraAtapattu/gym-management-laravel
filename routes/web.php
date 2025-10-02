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
        $mongo = 'MongoDB connection failed: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
    }

    return response()->json(compact('mysql', 'mongo'));
});

// Simple MongoDB test route
Route::get('/test-mongo', function () {
    try {
        // Check if MongoDB extension is loaded
        if (!extension_loaded('mongodb')) {
            throw new \Exception('MongoDB PHP extension is not installed or enabled');
        }
        
        $connectionString = env('MONGODB_URI', 'mongodb://127.0.0.1:27017');
        $databaseName = env('MONGODB_DATABASE', 'gym_management');
        
        // Create a new MongoDB client
        $client = new MongoDB\Client($connectionString);
        
        // Test the connection by pinging the server
        $response = $client->admin->command(['ping' => 1]);
        
        // List all databases
        $databases = $client->listDatabases();
        $dbNames = [];
        
        foreach ($databases as $database) {
            $dbNames[] = $database->getName();
        }
        
        // Try to access the specified database
        $database = $client->selectDatabase($databaseName);
        $collections = $database->listCollections();
        $collectionNames = [];
        
        foreach ($collections as $collection) {
            $collectionNames[] = $collection->getName();
        }
        
        return response()->json([
            'status' => 'success',
            'ping' => $response->toArray(),
            'databases' => $dbNames,
            'current_database' => $databaseName,
            'collections' => $collectionNames,
            'message' => 'Successfully connected to MongoDB',
            'connection_string' => $connectionString
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'MongoDB connection failed: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString())
        ], 500);
    }
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
