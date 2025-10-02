<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MongoLogger
{
    /**
     * Log an action to MongoDB
     *
     * @param string $action
     * @param array $data
     * @param string $collection
     * @return void
     */
    public static function log($action, $data = [], $collection = 'activity_logs')
    {
        try {
            $logData = [
                'action' => $action,
                'data' => $data,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (auth()->check()) {
                $logData['user_id'] = auth()->id();
            }

            DB::connection('mongodb')->collection($collection)->insert($logData);
        } catch (\Exception $e) {
            // Fallback to log file if MongoDB connection fails
            \Log::error('MongoDB Logging Error: ' . $e->getMessage());
        }
    }
}
