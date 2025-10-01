<?php

namespace App\Services;

use MongoDB\Client as MongoClient;
use MongoDB\Operation\BulkWrite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime as MongoUTCDateTime;
use Exception;
use ErrorException;

class MongoDBMigrator
{
    private $mysqlConnection;
    private $mongoClient;
    private $mongoDb;
    private $report = [];
    private $batchSize = 100; // Process documents in batches of 100

    public function __construct()
    {
        $this->mysqlConnection = DB::connection('mysql');
        
        $mongoConfig = config('database.connections.mongodb');
        $mongoUri = $mongoConfig['dsn'] ?? 'mongodb://localhost:27017';
        $mongoDb = $mongoConfig['database'] ?? 'laravel_sem2';
        
        $this->mongoClient = new MongoClient($mongoUri, [
            'tls' => $mongoConfig['options']['tls'] ?? false,
            'tlsInsecure' => config('app.env') !== 'production',
            'authSource' => $mongoConfig['options']['authSource'] ?? 'admin',
            'connectTimeoutMS' => 30000,
            'socketTimeoutMS' => 30000,
            'serverSelectionTimeoutMS' => 30000,
        ]);
        
        $this->mongoDb = $this->mongoClient->selectDatabase($mongoDb);
        
        Log::info('MongoDB Connection Established', [
            'database' => $mongoDb,
            'uri' => $mongoUri,
            'authSource' => $mongoConfig['options']['authSource'] ?? 'admin'
        ]);
        
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }

    public function migrate()
    {
        try {
            $tables = $this->getMySQLTables();
            $this->report['started_at'] = now()->toDateTimeString();
            $this->report['tables'] = [];

            foreach ($tables as $table) {
                $this->migrateTable($table);
            }

            $this->report['completed_at'] = now()->toDateTimeString();
            $this->generateReport();
            
            return $this->report;
        } catch (Exception $e) {
            Log::error('Migration failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getMySQLTables()
    {
        return $this->mysqlConnection->select('SHOW TABLES');
    }

    private function migrateTable($tableInfo)
    {
        $tableName = array_values((array)$tableInfo)[0];
        
        $tableReport = [
            'name' => $tableName,
            'rows_processed' => 0,
            'rows_inserted' => 0,
            'errors' => []
        ];

        try {
            $columns = $this->getTableColumns($tableName);
            $rows = $this->getTableData($tableName);
            $tableReport['rows_processed'] = count($rows);
            $tableReport['columns'] = array_column($columns, 'Field');

            if (!empty($rows)) {
                $documents = $this->convertToMongoDocuments($rows, $columns);
                $result = $this->insertIntoMongoDB($tableName, $documents);
                $tableReport['rows_inserted'] = $result->insertedCount + $result->upsertedCount;
                $tableReport['rows_modified'] = $result->modifiedCount;
            }

            Log::info("Migrated table: $tableName ({$tableReport['rows_inserted']} rows)");
        } catch (Exception $e) {
            $errorMsg = "Error migrating table $tableName: " . $e->getMessage();
            $tableReport['errors'][] = $errorMsg;
            Log::error($errorMsg);
        }

        $this->report['tables'][] = $tableReport;
    }

    private function getTableColumns($tableName)
    {
        return $this->mysqlConnection->select("SHOW COLUMNS FROM `$tableName`");
    }

    private function getTableData($tableName)
    {
        return $this->mysqlConnection->table($tableName)->get()->toArray();
    }

    private function convertToMongoDocuments($rows, $columns)
    {
        $documents = [];
        $columnMap = [];
        
        foreach ($columns as $column) {
            $columnMap[$column->Field] = $column->Type;
        }

        foreach ($rows as $row) {
            $document = [];
            foreach ((array)$row as $key => $value) {
                if ($value === null) {
                    $document[$key] = null;
                    continue;
                }

                $type = strtolower($columnMap[$key] ?? '');
                $document[$key] = $this->convertValue($value, $type);
            }

            // Handle primary key
            if (isset($document['id'])) {
                $document['_id'] = (string) $document['id'];
                unset($document['id']);
            }

            $documents[] = $document;
        }

        return $documents;
    }

    private function convertValue($value, $type)
    {
        if (strpos($type, 'int') === 0) {
            return (int) $value;
        } elseif (strpos($type, 'decimal') === 0 || 
                 strpos($type, 'float') === 0 || 
                 strpos($type, 'double') === 0) {
            return (float) $value;
        } elseif (strpos($type, 'bool') === 0) {
            return (bool) $value;
        } elseif (strpos($type, 'json') === 0) {
            return is_string($value) ? json_decode($value, true) ?? $value : $value;
        } elseif (strpos($type, 'date') === 0 || 
                 strpos($type, 'time') === 0) {
            try {
                if (empty($value)) {
                    return null;
                }
                
                // Handle different date formats
                if (is_numeric($value)) {
                    $timestamp = (int)$value;
                } elseif ($value instanceof \DateTime) {
                    $timestamp = $value->getTimestamp();
                } else {
                    $timestamp = strtotime($value);
                    if ($timestamp === false) {
                        throw new \Exception("Invalid date format: " . (string)$value);
                    }
                }
                
                // Ensure we have a valid timestamp
                if ($timestamp <= 0) {
                    throw new \Exception("Invalid timestamp: " . $timestamp);
                }
                
                // Convert to milliseconds for MongoDB
                $milliseconds = $timestamp * 1000;
                
                // Use our safe method to create UTCDateTime
                return $this->createUTCDateTime($milliseconds);
                
            } catch (\Exception $e) {
                Log::warning(sprintf(
                    'Date conversion error for value "%s": %s',
                    is_scalar($value) ? (string)$value : gettype($value),
                    $e->getMessage()
                ));
                return $value; // Return original value if conversion fails
            }
        }
        return $value;
    }

    private function insertIntoMongoDB($collectionName, $documents)
    {
        if (empty($documents)) {
            Log::info("No documents to insert into collection: " . $collectionName);
            return (object)['insertedCount' => 0, 'modifiedCount' => 0, 'upsertedCount' => 0];
        }
        
        $collection = $this->mongoDb->selectCollection($collectionName);
        $operations = [];
        $result = (object)[
            'insertedCount' => 0,
            'modifiedCount' => 0,
            'upsertedCount' => 0
        ];
        
        try {
            // Process documents in batches of 100
            $batchSize = 100;
            $totalDocuments = count($documents);
            $processed = 0;
            
            while ($processed < $totalDocuments) {
                $batch = array_slice($documents, $processed, $batchSize);
                $batchOperations = [];
                
                foreach ($batch as $doc) {
                    if (!isset($doc['_id'])) {
                        Log::warning("Document missing _id field", ['collection' => $collectionName]);
                        continue;
                    }
                    
                    $batchOperations[] = [
                        'updateOne' => [
                            ['_id' => $doc['_id']],
                            ['$set' => $doc],
                            ['upsert' => true]
                        ]
                    ];
                }
                
                if (!empty($batchOperations)) {
                    $batchResult = $collection->bulkWrite($batchOperations);
                    $result->insertedCount += $batchResult->getInsertedCount();
                    $result->modifiedCount += $batchResult->getModifiedCount();
                    $result->upsertedCount += $batchResult->getUpsertedCount();
                }
                
                $processed += count($batch);
                $remaining = $totalDocuments - $processed;
                Log::info(sprintf(
                    'Processed %d/%d documents (%.1f%%) for collection: %s',
                    $processed,
                    $totalDocuments,
                    ($processed / $totalDocuments) * 100,
                    $collectionName
                ));
            }
            
            $totalProcessed = $result->insertedCount + $result->modifiedCount + $result->upsertedCount;
            Log::info(sprintf(
                'Completed processing %s: %d inserted, %d modified, %d upserted (total: %d)',
                $collectionName,
                $result->insertedCount,
                $result->modifiedCount,
                $result->upsertedCount,
                $totalProcessed
            ));
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error(sprintf(
                'Error processing collection %s: %s',
                $collectionName,
                $e->getMessage()
            ), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return partial results if available
            if (isset($result)) {
                return $result;
            }
            
            // If we don't have a result yet, rethrow the exception
            throw $e;
        }
    }

    /**
     * Safely create a MongoDB UTCDateTime object
     */
    private function createUTCDateTime($milliseconds)
    {
        try {
            if (class_exists('MongoDB\\BSON\\UTCDateTime')) {
                return new MongoUTCDateTime($milliseconds);
            }
            
            // Fallback for when the MongoDB extension is not properly loaded
            if (class_exists('MongoDB\BSON\UTCDateTime', false)) {
                $class = new \ReflectionClass('MongoDB\BSON\UTCDateTime');
                return $class->newInstance($milliseconds);
            }
            
            // If we can't create a UTCDateTime, return the original value
            Log::warning('Could not create UTCDateTime, returning original value');
            return $milliseconds;
            
        } catch (\Exception $e) {
            Log::error('Failed to create UTCDateTime: ' . $e->getMessage() . ' - Using original value instead');
            return $milliseconds;
        }
    }

    private function generateReport()
    {
        $timestamp = now()->format('Ymd_His');
        $reportFile = storage_path("logs/migration_report_{$timestamp}.json");
        
        file_put_contents($reportFile, json_encode($this->report, JSON_PRETTY_PRINT));
        
        Log::info("Migration completed. Report saved to: $reportFile");
    }
}
