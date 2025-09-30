<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SeedMongoDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-mongo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the MongoDB database with test data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ensure we're using MongoDB
        Config::set('database.default', 'mongodb');
        
        $this->info('Seeding MongoDB database...');
        
        // Run migrations
        $this->call('migrate:fresh');
        
        // Seed the database
        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\MongoDatabaseSeeder',
            '--force' => true,
        ]);
        
        $this->info('MongoDB database seeded successfully!');
    }
}
