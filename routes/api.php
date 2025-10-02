<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

// Public API routes
Route::middleware('api')->group(function () {
    // MongoDB API routes
    Route::prefix('mongo')->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        // Add other MongoDB API routes here
    });
    
    // MySQL API routes can be added here without the 'mongo' prefix
});

// Add authentication middleware if needed
Route::middleware('auth:api')->group(function () {
    // Protected API routes
});
