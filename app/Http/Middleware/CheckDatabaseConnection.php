<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Exception;

class CheckDatabaseConnection
{
    public function handle($request, Closure $next)
    {
        // Skip for API requests
        if ($request->is('api/*')) {
            return $next($request);
        }

        // Check if we can connect to the database
        try {
            DB::connection()->getPdo();
            return $next($request);
        } catch (Exception $e) {
            Log::error('Database connection failed: ' . $e->getMessage());
            
            // Return a maintenance response
            return response()->view('errors.maintenance', [
                'message' => 'The database is currently unavailable. Please try again later.'
            ], 503);
        }
    }
}
