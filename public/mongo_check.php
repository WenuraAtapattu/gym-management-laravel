<?php

require __DIR__.'/../vendor/autoload.php';

use MongoDB\Client;

try {
    // Get MongoDB URI from environment variable
    $uri = getenv('MONGODB_URI');
    
    if (!$uri) {
        die("MONGODB_URI environment variable not set\n");
    }
    
    echo "<h2>MongoDB Connection Test</h2>";
    echo "<p>Connecting to: " . preg_replace('/:[^:]+@/', ':***@', $uri) . "</p>";
    
    // Create a new client and connect to the server
    $client = new Client($uri);
    
    // List databases
    echo "<h3>Available Databases:</h3>";
    $databases = $client->listDatabases();
    
    echo "<ul>";
    foreach ($databases as $database) {
        $dbName = $database->getName();
        echo "<li><strong>$dbName</strong>";
        
        // List collections in the database
        $db = $client->selectDatabase($dbName);
        $collections = $db->listCollections();
        
        echo "<ul>";
        foreach ($collections as $collection) {
            $collectionName = $collection->getName();
            $count = $db->$collectionName->countDocuments();
            echo "<li>$collectionName ($count documents)";
            
            // Show first few documents
            $documents = $db->$collectionName->find()->limit(3);
            echo "<ul>";
            foreach ($documents as $doc) {
                echo "<li>" . json_encode($doc, JSON_PRETTY_PRINT) . "</li>";
            }
            echo "</ul>";
            echo "</li>";
        }
        echo "</ul>";
        echo "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<div style='color:red;'><h3>Error:</h3><pre>" . htmlspecialchars($e->getMessage()) . "</pre></div>";
}
?>
