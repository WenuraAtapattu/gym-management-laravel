<?php

require __DIR__.'/../vendor/autoload.php';

use MongoDB\Client;

try {
    // Get MongoDB URI from environment variable
    $uri = getenv('MONGODB_URI');
    
    if (!$uri) {
        die("MONGODB_URI environment variable not set\n");
    }
    
    // Log the connection attempt (without password)
    $logUri = preg_replace('/:[^:]+@/', ':***@', $uri);
    echo "Attempting to connect to: $logUri\n\n";
    
    // Create a new client and connect to the server
    $client = new Client($uri);
    
    // Send a ping to confirm a successful connection
    $client->selectDatabase('admin')->command(['ping' => 1]);
    
    echo "✅ Successfully connected to MongoDB.\n\n";
    
    // List databases
    echo "Listing databases:\n";
    $databases = $client->listDatabases();
    
    foreach ($databases as $database) {
        echo "- " . $database->getName() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
}
