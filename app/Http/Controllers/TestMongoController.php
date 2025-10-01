<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use MongoDB\Client as MongoClient;
use MongoDB\Driver\Exception\ConnectionTimeoutException as MongoConnectionTimeoutException;
use MongoDB\Driver\Exception\RuntimeException as MongoRuntimeException;

class TestMongoController extends Controller
{
    public function testConnection(): JsonResponse
    {
        $config = config('database.connections.mongodb', []);
        
        if (empty($config)) {
            return response()->json([
                'status' => 'error',
                'message' => 'MongoDB configuration not found'
            ], 500);
        }

        try {
            $client = new MongoClient($config['dsn'], $config['options'] ?? []);
            // Try to list databases to test the connection
            $client->listDatabases();

            return response()->json([
                'status' => 'success',
                'message' => 'MongoDB connection successful',
                'server_info' => $client->getManager()->getServers()
            ]);

        } catch (MongoConnectionTimeoutException $e) {
            return $this->handleError('MongoDB connection timeout: ' . $e->getMessage(), $e);
        } catch (MongoRuntimeException $e) {
            return $this->handleError('MongoDB runtime error: ' . $e->getMessage(), $e);
        } catch (\Exception $e) {
            return $this->handleError('Unexpected error', $e);
        }
    }

    private function buildDsn(array $config): string
{
    $dsn = 'mongodb://';
    
    if (!empty($config['username']) && !empty($config['password'])) {
        $dsn .= rawurlencode($config['username']) . ':' . 
                rawurlencode($config['password']) . '@';
    }
    
    $dsn .= ($config['host'] ?? '127.0.0.1') . ':' . ($config['port'] ?? 27017);
    
    $query = [];
    if (!empty($config['database'])) {
        $query['authSource'] = $config['options']['authSource'] ?? 'admin';
    }
    
    if (!empty($config['database'])) {
        $dsn .= '/' . $config['database'];
    }
    
    if (!empty($query)) {
        $dsn .= '?' . http_build_query($query);
    }
    
    return $dsn;
}

    private function handleError(string $message, \Exception $e): JsonResponse
    {
        $error = [
            'status' => 'error',
            'message' => $message,
            'error' => $e->getMessage()
        ];

        if (config('app.debug')) {
            $error['trace'] = $e->getTraceAsString();
        }

        return response()->json($error, 500);
    }
}