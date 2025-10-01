<?php

require __DIR__.'/vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime as MongoUTCDateTime;

// Test MongoDB connection
$uri = 'mongodb+srv://heroku_user:798200305v@cluster0.qi715iy.mongodb.net/laravel_sem2?retryWrites=true&w=majority';

try {
    // Test basic connection
    $client = new Client($uri, [
        'tls' => true,
        'authSource' => 'admin',
        'retryWrites' => true,
        'w' => 'majority',
        'serverSelectionTimeoutMS' => 10000,
        'socketTimeoutMS' => 45000,
        'connectTimeoutMS' => 10000,
    ]);

    // Test listing databases
    $databases = $client->listDatabases();
    echo "✅ Successfully connected to MongoDB!\n";
    echo "Available databases:\n";
    
    foreach ($databases as $database) {
        echo "- " . $database->getName() . "\n";
    }
    
    // Test UTCDateTime
    $now = new MongoUTCDateTime(time() * 1000);
    echo "\n✅ Successfully created UTCDateTime: " . get_class($now) . "\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

// Test Laravel's MongoDB connection
require __DIR__.'/bootstrap/app.php';

$app->make(Illine\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Test Laravel's MongoDB connection
    $collections = DB::connection('mongodb')
        ->getMongoClient()
        ->selectDatabase('laravel_sem2')
        ->listCollections();
        
    echo "\n✅ Successfully connected via Laravel's MongoDB connection!\n";
    echo "Available collections in laravel_sem2:\n";
    
    foreach ($collections as $collection) {
        echo "- " . $collection->getName() . "\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ Laravel MongoDB connection failed: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
