<?php
require 'vendor/autoload.php';

try {
    echo "Testing MongoDB connection...\n";
    
    // Check if MongoDB extension is loaded
    if (!extension_loaded('mongodb')) {
        throw new Exception('MongoDB PHP extension is not installed or enabled');
    }
    
    $uri = 'mongodb://localhost:27017';
    $client = new MongoDB\Client($uri);
    
    // Test the connection by pinging the server
    $ping = $client->admin->command(['ping' => 1]);
    echo "MongoDB ping successful: " . json_encode($ping) . "\n";
    
    // List databases
    $databases = $client->listDatabases();
    echo "\nAvailable databases:\n";
    foreach ($databases as $database) {
        echo "- " . $database->getName() . "\n";
    }
    
    // Test specific database
    $dbName = 'gym_management';
    $database = $client->selectDatabase($dbName);
    echo "\nCollections in $dbName:\n";
    $collections = $database->listCollections();
    foreach ($collections as $collection) {
        echo "- " . $collection->getName() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
