<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestLaravelMongoDB extends Command
{
    protected $signature = 'test:laravel-mongodb';
    protected $description = 'Test MongoDB connection using Laravel DB facade';

    public function handle()
    {
        try {
            // Test connection
            DB::connection('mongodb')->getMongoClient()->listDatabases();
            $this->info('✅ Successfully connected to MongoDB via Laravel!');
            
            // List collections in the laravel_sem2 database
            $collections = DB::connection('mongodb')
                ->getMongoDB()
                ->listCollections();
                
            $this->info('\nAvailable collections in laravel_sem2:');
            foreach ($collections as $collection) {
                $this->line('- ' . $collection->getName());
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ MongoDB connection failed: ' . $e->getMessage());
            return 1;
        }
    }
}
