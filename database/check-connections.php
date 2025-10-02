<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Test MySQL Connection
    echo "Testing MySQL connection...\n";
    $mysql = new PDO(
        'mysql:host=' . env('DB_HOST') . ';port=' . env('DB_PORT') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD')
    );
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ MySQL connection successful!\n";
    echo "MySQL Server Version: " . $mysql->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
    
} catch (PDOException $e) {
    echo "❌ MySQL Connection failed: " . $e->getMessage() . "\n\n";
}

try {
    // Test MongoDB Connection
    echo "Testing MongoDB connection...\n";
    $mongo = new MongoDB\Client(env('MONGODB_ATLAS_URI'));
    $dbs = $mongo->listDatabases();
    echo "✅ MongoDB connection successful!\n";
    echo "MongoDB Server Version: " . $mongo->getManager()->getServer()->getInfo()['version'] . "\n";
    
} catch (Exception $e) {
    echo "❌ MongoDB Connection failed: " . $e->getMessage() . "\n";
}
