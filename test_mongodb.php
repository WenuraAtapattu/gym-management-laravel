<?php

require __DIR__.'/vendor/autoload.php';

$uri = 'mongodb+srv://heroku_user:798200305v@cluster0.qi715iy.mongodb.net/laravel_sem2?retryWrites=true&w=majority';

try {
    $client = new MongoDB\Client($uri);
    $database = $client->selectDatabase('laravel_sem2');
    $collections = $database->listCollections();
    
    echo "✅ Successfully connected to MongoDB Atlas!\n";
    echo "Available collections:\n";
    
    foreach ($collections as $collection) {
        echo "- " . $collection->getName() . "\n";
    }
} catch (Exception $e) {
    echo "❌ MongoDB connection failed: " . $e->getMessage() . "\n";
}
