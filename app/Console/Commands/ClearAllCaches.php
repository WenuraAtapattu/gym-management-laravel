<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearAllCaches extends Command
{
    protected $signature = 'cache:clear-all';
    protected $description = 'Clear all caches without using the database';

    public function handle()
    {
        $this->call('config:clear');
        $this->call('view:clear');
        $this->call('route:clear');
        
        // Clear the file cache
        $cachePath = storage_path('framework/cache/data');
        if (File::exists($cachePath)) {
            File::cleanDirectory($cachePath);
            $this->info('File cache cleared!');
        }
        
        // Clear compiled files
        $compiledPath = storage_path('framework/views');
        if (File::exists($compiledPath)) {
            File::cleanDirectory($compiledPath);
            $this->info('Compiled views cleared!');
        }
        
        $this->info('All caches cleared successfully!');
        return 0;
    }
}
