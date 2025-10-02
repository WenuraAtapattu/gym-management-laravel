<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

try {
    // Test MySQL connection
    $mysql = new PDO(
        'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "MySQL connection successful!\n";
} catch (PDOException $e) {
    echo "MySQL connection failed: " . $e->getMessage() . "\n";
}

try {
    // Test MongoDB connection
    $mongo = new MongoDB\Client(getenv('MONGODB_URI'));
    $mongo->listDatabases();
    echo "MongoDB connection successful!\n";
} catch (Exception $e) {
    echo "MongoDB connection failed: " . $e->getMessage() . "\n";
}
