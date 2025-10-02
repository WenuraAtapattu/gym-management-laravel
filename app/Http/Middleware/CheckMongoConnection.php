<?php

namespace App\Http\Middleware;

use Closure;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use MongoDB\Driver\Exception\RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckMongoConnection
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $config = config('database.connections.mongodb', []);
            
            if (empty($config) || empty($config['dsn'])) {
                return $this->errorResponse('MongoDB configuration not found or invalid', 500);
            }

            // Test MongoDB connection
            $client = new \MongoDB\Client($config['dsn'], $config['options'] ?? []);
            $client->listDatabases();
            
            return $next($request);

        } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            return $this->errorResponse('MongoDB connection timeout', 503);
        } catch (\MongoDB\Driver\Exception\RuntimeException $e) {
            return $this->errorResponse('MongoDB error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse('Unexpected error: ' . $e->getMessage(), 500);
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
        
        if (!empty($config['database'])) {
            $dsn .= '/' . $config['database'];
        }
        
        return $dsn;
    }

    private function errorResponse(string $message, int $status = 500): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];

        if (config('app.debug')) {
            $response['debug'] = [
                'time' => now()->toDateTimeString(),
                'env' => config('app.env')
            ];
        }

        return response()->json($response, $status);
    }
}