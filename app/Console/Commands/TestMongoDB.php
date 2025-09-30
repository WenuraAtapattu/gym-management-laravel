<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class TestMongoDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mongodb {--filter= : The filter to apply to the test suite}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run tests with MongoDB configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Set MongoDB as the default connection for testing
        Config::set('database.default', 'mongodb');
        
        $filter = $this->option('filter');
        $command = './vendor/bin/phpunit';
        $config = '--configuration=phpunit.mongodb.xml';
        
        if ($filter) {
            $command .= " --filter={$filter}";
        }
        
        $this->info('Running MongoDB tests...');
        
        // Run the tests
        $exitCode = 0;
        passthru("$command $config", $exitCode);
        
        return $exitCode;
    }
}
