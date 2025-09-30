<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MongoDB\Client as MongoClient;
use Exception;

class MigrateMySQLToMongo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:mysql-to-mongo {table? : (Optional) Specific MySQL table to migrate. Leave empty to migrate all tables.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from MySQL to MongoDB';

    /**
     * Tables to exclude from migration
     *
     * @var array
     */
    protected $excludedTables = [
        'migrations',
        'password_reset_tokens',
        'failed_jobs',
        'personal_access_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
    ];

    /**
     * MongoDB client instance
     *
     * @var \MongoDB\Client
     */
    protected $mongoClient;

    /**
     * MongoDB database instance
     *
     * @var \MongoDB\Database
     */
    protected $mongoDb;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        // Initialize MongoDB client
        $host = config('database.connections.mongodb.host', '127.0.0.1');
        $port = config('database.connections.mongodb.port', '27017');
        $database = config('database.connections.mongodb.database', 'gym_management');
        $username = config('database.connections.mongodb.username');
        $password = config('database.connections.mongodb.password');
        
        $dsn = "mongodb://";
        if ($username && $password) {
            $dsn .= "{$username}:{$password}@";
        }
        $dsn .= "{$host}:{$port}";
        
        $this->mongoClient = new MongoClient($dsn);
        $this->mongoDb = $this->mongoClient->selectDatabase($database);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $specificTable = $this->argument('table');
        
        if ($specificTable) {
            return $this->migrateTable($specificTable);
        }

        // Get all tables from the database
        $tables = $this->getAllTables();
        
        if (empty($tables)) {
            $this->error('No tables found in the database.');
            return 1;
        }

        $this->info('Starting migration of all tables from MySQL to MongoDB...');
        
        $migratedTables = 0;
        $totalRecords = 0;
        $failedTables = [];

        foreach ($tables as $table) {
            if (in_array($table, $this->excludedTables)) {
                $this->line("<fg=yellow>Skipping excluded table:</> {$table}");
                continue;
            }

            $this->line("\nMigrating table: <fg=blue>{$table}</>");
            
            try {
                $recordCount = $this->migrateTable($table);
                if ($recordCount >= 0) {
                    $migratedTables++;
                    $totalRecords += $recordCount;
                    $this->line("<fg=green>✓</> Successfully migrated {$recordCount} records from '{$table}'");
                } else {
                    $failedTables[] = $table;
                }
            } catch (\Exception $e) {
                $this->error("Error migrating table '{$table}': " . $e->getMessage());
                $failedTables[] = $table;
            }
        }

        // Display summary
        $this->newLine(2);
        $this->info("Migration Summary:");
        $this->line("• Successfully migrated {$migratedTables} tables");
        $this->line("• Total records migrated: {$totalRecords}");
        
        if (!empty($failedTables)) {
            $this->newLine();
            $this->warn("The following tables failed to migrate:");
            foreach ($failedTables as $failedTable) {
                $this->line("- {$failedTable}");
            }
            $this->newLine();
            $this->info('You can try migrating failed tables individually using:');
            $this->line('php artisan migrate:mysql-to-mongo table_name');
        }

        return 0;
    }

    /**
     * Get all tables from the database
     *
     * @return array
     */
    protected function getAllTables()
    {
        $database = DB::getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        
        $key = 'Tables_in_' . $database;
        $tableNames = [];
        
        foreach ($tables as $table) {
            $tableNames[] = $table->$key;
        }
        
        return $tableNames;
    }

    /**
     * Migrate a single table
     *
     * @param string $table
     * @return int Number of records migrated, or -1 on failure
     */
    protected function migrateTable($table)
    {
        if (!Schema::hasTable($table)) {
            $this->error("The table '{$table}' does not exist in MySQL.");
            return -1;
        }

        try {
            // Get records from MySQL
            $records = DB::table($table)->get();
            
            if ($records->isEmpty()) {
                $this->warn("No records found in the '{$table}' table.");
                return 0;
            }

            // Convert to array for MongoDB
            $documents = $records->map(function ($item) {
                return (array) $item;
            })->toArray();

            // Get MongoDB collection
            $collection = $this->mongoDb->selectCollection($table);
            
            // Clear existing data in the collection
            $collection->deleteMany([]);

            // Insert into MongoDB in chunks to handle large datasets
            $chunks = array_chunk($documents, 1000);
            $insertedCount = 0;

            foreach ($chunks as $chunk) {
                $result = $collection->insertMany($chunk);
                $insertedCount += $result->getInsertedCount();
                $this->output->write('.'); // Show progress
            }

            $this->newLine(); // Move to next line after progress dots
            return $insertedCount;

        } catch (\Exception $e) {
         $this->error("Error migrating '{$table}': " . $e->getMessage());
            return -1;
        }
    }
}
