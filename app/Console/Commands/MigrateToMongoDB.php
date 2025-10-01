<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\MongoDBMigrator;

class MigrateToMongoDB extends Command
{
    protected $signature = 'db:migrate-to-mongodb';
    protected $description = 'Migrate data from MySQL to MongoDB';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting MySQL to MongoDB migration...');
        
        try {
            $migrator = new MongoDBMigrator();
            $result = $migrator->migrate();
            
            $this->info('Migration completed successfully!');
            $this->info('Summary:');
            
            foreach ($result['tables'] ?? [] as $table) {
                $status = empty($table['errors']) ? '✅' : '❌';
                $this->line("$status {$table['name']}: {$table['rows_processed']} processed, {$table['rows_inserted']} inserted");
                
                if (!empty($table['errors'])) {
                    foreach ($table['errors'] as $error) {
                        $this->error("  - $error");
                    }
                }
            }
            
            $this->info("\nDetailed report saved to: " . storage_path('logs/migration_report_*.json'));
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            Log::error('Migration failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }
    }
}
