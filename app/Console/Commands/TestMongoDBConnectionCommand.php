<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client as MongoClient;

class TestMongoDBConnectionCommand extends Command
{
    protected $signature = 'test:mongodb-connection';
    protected $description = 'Test MongoDB connection using the MongoDB PHP driver directly';

    public function handle()
    {
        try {
            $uri = 'mongodb+srv://heroku_user:798200305v@cluster0.qi715iy.mongodb.net/laravel_sem2?retryWrites=true&w=majority';
            
            $client = new MongoClient($uri, [
                'tls' => true,
                'authSource' => 'admin',
                'retryWrites' => true,
                'w' => 'majority',
                'serverSelectionTimeoutMS' => 10000,
                'socketTimeoutMS' => 45000,
                'connectTimeoutMS' => 10000,
            ]);
            
            // Try to list databases to verify connection
            $databases = $client->listDatabases();
            
            $this->info('✅ Successfully connected to MongoDB!');
            $this->info('Available databases:');
            
            foreach ($databases as $database) {
                $this->line('- ' . $database->getName());
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ MongoDB connection failed: ' . $e->getMessage());
            return 1;
        }
    }
}
