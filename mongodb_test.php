<?php

// Include Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

// Test MongoDB extension and classes
$extensions = get_loaded_extensions();
echo "Loaded extensions: " . implode(", ", $extensions) . "\n";

// Check if MongoDB extension is loaded
if (!extension_loaded('mongodb')) {
    die("MongoDB extension is not loaded!\n");
}

// List all MongoDB classes
$mongodbClasses = array_filter(
    get_declared_classes(),
    function($className) {
        return strpos($className, 'MongoDB\\') === 0;
    }
);

echo "\nMongoDB classes found: " . count($mongodbClasses) . "\n";
foreach ($mongodbClasses as $class) {
    echo "- $class\n";
}

// Test MongoDB connection
try {
    echo "\nTesting MongoDB connection...\n";
    
    $client = new MongoDB\Client(
        'mongodb://localhost:27017',
        [
            'username' => null,
            'password' => null,
            'authSource' => 'admin',
            'ssl' => false,
        ]
    );
    
    $databases = $client->listDatabases();
    echo "Connected to MongoDB successfully!\n";
    echo "Available databases:\n";
    
    foreach ($databases as $database) {
        echo "- " . $database->getName() . "\n";
    }
    
} catch (Exception $e) {
    echo "MongoDB connection error: " . $e->getMessage() . "\n";
}
