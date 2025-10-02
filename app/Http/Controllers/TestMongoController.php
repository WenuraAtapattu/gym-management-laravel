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
        try {
            $config = config('database.connections.mongodb', []);
            
            if (empty($config) || empty($config['dsn'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'MongoDB configuration not found or invalid',
                    'config' => $config
                ], 500);
            }

            $client = new \MongoDB\Client($config['dsn'], $config['options'] ?? []);
            $databases = $client->listDatabases();
            
            return response()->json([
                'status' => 'success',
                'message' => 'MongoDB connection successful',
                'database' => $config['database'] ?? 'Not specified',
                'server_info' => [
                    'server' => $client->getManager()->getServers(),
                    'uri' => $config['dsn']
                ]
            ]);

        } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            return $this->handleError('MongoDB connection timeout: ' . $e->getMessage(), $e);
        } catch (\MongoDB\Driver\Exception\RuntimeException $e) {
            return $this->handleError('MongoDB runtime error: ' . $e->getMessage(), $e);
        } catch (\Exception $e) {
            return $this->handleError('Unexpected error: ' . $e->getMessage(), $e);
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