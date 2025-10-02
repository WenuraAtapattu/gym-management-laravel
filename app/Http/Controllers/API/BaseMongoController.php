<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseMongoController extends Controller
{
    protected $mongoConnection;
    
    public function __construct()
    {
        try {
            $this->mongoConnection = DB::connection('mongodb');
        } catch (\Exception $e) {
            Log::error('MongoDB connection failed: ' . $e->getMessage());
            abort(500, 'Unable to connect to the database');
        }
    }
}
