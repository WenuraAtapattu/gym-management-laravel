<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class MongoDBMigrator
{
    protected $mongoConnection;
    protected $mysqlConnection = null;

    public function __construct()
    {
        try {
            // Initialize MongoDB connection
            $this->mongoConnection = DB::connection('mongodb');
            Log::info('MongoDB connection initialized successfully');
            
            // Only try to connect to MySQL if it's explicitly needed
            if ($this->isMySQLNeeded()) {
                $this->initializeMySQLConnection();
            }
        } catch (Exception $e) {
            Log::error('Database connection error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Check if MySQL connection is needed
     */
    protected function isMySQLNeeded(): bool
    {
        // Add logic here if you need to check if MySQL is required
        return false; // Default to false since we're migrating to MongoDB
    }

    /**
     * Initialize MySQL connection if needed
     */
    protected function initializeMySQLConnection(): void
    {
        try {
            $this->mysqlConnection = DB::connection('mysql');
            Log::info('MySQL connection initialized successfully');
        } catch (Exception $e) {
            Log::warning('Failed to initialize MySQL connection', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if MySQL is available
     */
    public function isMySQLAvailable(): bool
    {
        if ($this->mysqlConnection === null) {
            $this->initializeMySQLConnection();
        }
        return $this->mysqlConnection !== null;
    }

    /**
     * Get MongoDB connection
     */
    public function getMongoConnection()
    {
        return $this->mongoConnection;
    }

    /**
     * Get MySQL connection if available
     */
    public function getMySQLConnection()
    {
        if (!$this->isMySQLAvailable()) {
            throw new \RuntimeException('MySQL connection is not available');
        }
        return $this->mysqlConnection;
    }
}
