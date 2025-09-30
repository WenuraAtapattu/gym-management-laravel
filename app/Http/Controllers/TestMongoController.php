<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use MongoDB\Driver\Exception\RuntimeException;

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
            $manager = new Manager($this->buildDsn($config));
            $command = new Command(['ping' => 1]);
            $manager->executeCommand('admin', $command);

            return response()->json([
                'status' => 'success',
                'message' => 'MongoDB connection successful'
            ]);

        } catch (ConnectionTimeoutException $e) {
            return $this->handleError('Connection timeout', $e);
        } catch (RuntimeException $e) {
            return $this->handleError('MongoDB error', $e);
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