<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetMongoDBConfig extends Command
{
    protected $signature = 'mongodb:set-config';
    protected $description = 'Update MongoDB configuration in .env file';

    public function handle()
    {
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return 1;
        }

        $connectionString = $this->ask('Enter your MongoDB Atlas connection string (e.g., mongodb+srv://user:password@cluster0.qi715iy.mongodb.net/)');
        $database = $this->ask('Enter your database name', 'laravel_sem2');
        
        // Parse the connection string
        $parsed = parse_url($connectionString);
        $username = $parsed['user'] ?? '';
        $password = $parsed['pass'] ?? '';
        $host = $parsed['host'] ?? 'localhost';
        $port = $parsed['port'] ?? '27017';
        
        // Build the connection URI
        $uri = "mongodb+srv://{$username}:{$password}@{$host}/{$database}?retryWrites=true&w=majority";
        
        // Update .env file
        $envContent = File::get($envPath);
        
        $patterns = [
            '/MONGODB_URI=.*/' => "MONGODB_URI={$uri}",
            '/MONGODB_DATABASE=.*/' => "MONGODB_DATABASE={$database}",
            '/MONGODB_USERNAME=.*/' => "MONGODB_USERNAME={$username}",
            '/MONGODB_PASSWORD=.*/' => "MONGODB_PASSWORD={$password}",
            '/MONGODB_AUTH_SOURCE=.*/' => 'MONGODB_AUTH_SOURCE=admin',
            '/DB_CONNECTION=.*/' => 'DB_CONNECTION=mysql',
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            $envContent = preg_replace($pattern, $replacement, $envContent);
        }
        
        File::put($envPath, $envContent);
        
        $this->info('✅ MongoDB configuration updated successfully!');
        $this->info('Please run: php artisan config:clear');
        
        return 0;
    }
}
