<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('clear:all', function () {
    $this->call('cache:clear');
    $this->call('config:clear');
    $this->call('route:clear');
    $this->call('view:clear');
    $this->call('optimize:clear');
    
    // Clear application cache
    Cache::flush();
    
    $this->info('All caches cleared successfully!');
})->purpose('Clear all caches and compiled views');
