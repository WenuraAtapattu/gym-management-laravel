<?php

require __DIR__.'/vendor/autoload.php';

use MongoDB\Client;

try {
    // Get the MongoDB URI from environment variables
    $uri = getenv('MONGODB_URI');
    
    if (!$uri) {
        die("MONGODB_URI environment variable not set\n");
    }
    
    echo "Attempting to connect to MongoDB with URI: " . preg_replace('/:[^:]+@/', ':***@', $uri) . "\n";
    
    // Create a new client and connect to the server
    $client = new Client($uri);
    
    // Send a ping to confirm a successful connection
    $client->selectDatabase('admin')->command(['ping' => 1]);
    echo "Successfully connected to MongoDB.\n";
    
    // List databases
    $databases = $client->listDatabases();
    echo "Available databases:\n";
    foreach ($databases as $database) {
        echo "- " . $database->getName() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
