<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client as MongoClient;

class TestMongoDBConnection extends Command
{
    protected $signature = 'mongodb:test-connection';
    protected $description = 'Test MongoDB connection';

    public function handle()
    {
        try {
            $client = new MongoClient(env('MONGODB_URI'));
            $client->listDatabases();
            $this->info('✅ MongoDB connection successful!');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ MongoDB connection failed: ' . $e->getMessage());
            return 1;
        }
    }
}
