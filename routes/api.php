<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Use fully qualified class names to avoid case sensitivity issues
// Note: Using FQCN to ensure consistent case handling

// Authentication
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// MongoDB API Routes
Route::prefix('mongo')->group(function () {
    // Test MongoDB Connection
    Route::get('/test', [\App\Http\Controllers\TestMongoController::class, 'testConnection']);

    // Public review routes (read-only)
    Route::get('products/{product}/reviews', [\App\Http\Controllers\API\MongoReviewController::class, 'index'])
        ->name('products.reviews.public.index');
    Route::get('products/{product}/reviews/{review}', [\App\Http\Controllers\API\MongoReviewController::class, 'show'])
        ->name('products.reviews.public.show');

    // Protected MongoDB routes
    Route::middleware('auth:sanctum')->group(function () {
        // Users
        Route::apiResource('users', \App\Http\Controllers\API\MongoUserController::class);
        
        // Products
        Route::apiResource('products', \App\Http\Controllers\API\MongoProductController::class);
        
        // Reviews CRUD
        Route::post('products/{product}/reviews', [\App\Http\Controllers\API\MongoReviewController::class, 'store'])
            ->name('products.reviews.store');
        Route::put('products/{product}/reviews/{review}', [\App\Http\Controllers\API\MongoReviewController::class, 'update'])
            ->name('products.reviews.update');
        Route::delete('products/{product}/reviews/{review}', [\App\Http\Controllers\API\MongoReviewController::class, 'destroy'])
            ->name('products.reviews.destroy');
            
        // Review stats
        Route::get('reviews/stats', [\App\Http\Controllers\API\MongoReviewController::class, 'stats']);
        Route::get('products/{product}/reviews/stats', [\App\Http\Controllers\API\MongoReviewController::class, 'stats']);
    });
});

// MySQL API Routes
Route::middleware('auth:sanctum')->group(function () {
    // Payments
    Route::apiResource('payments', \App\Http\Controllers\API\PaymentController::class);
    Route::get('payments/member/{user}', [\App\Http\Controllers\API\PaymentController::class, 'getMemberPayments']);
    Route::get('payments/date-range', [\App\Http\Controllers\API\PaymentController::class, 'getPaymentsByDateRange']);
    Route::post('payments/{payment}/status', [\App\Http\Controllers\API\PaymentController::class, 'updateStatus']);

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\CartController::class, 'index']);
        Route::post('/items/{product}', [\App\Http\Controllers\API\CartController::class, 'store']);
        Route::put('/items/{cartItem}', [\App\Http\Controllers\API\CartController::class, 'update']);
        Route::delete('/items/{cartItem}', [\App\Http\Controllers\API\CartController::class, 'destroy']);
        Route::post('/clear', [\App\Http\Controllers\API\CartController::class, 'clear']);
        Route::get('/count', [\App\Http\Controllers\API\CartController::class, 'count']);
    });

    // MySQL Reviews
    Route::apiResource('products.reviews', \App\Http\Controllers\API\ReviewController::class);
    Route::post('reviews/{review}/report', [\App\Http\Controllers\API\ReviewController::class, 'report'])
        ->name('reviews.report');

    // Admin routes
    Route::prefix('admin')->middleware(['can:manage_reviews'])->group(function () {
        Route::get('reviews/pending', [\App\Http\Controllers\API\Admin\AdminReviewController::class, 'pendingReviews'])
            ->name('admin.reviews.pending');
        Route::post('reviews/{review}/approve', [\App\Http\Controllers\API\Admin\AdminReviewController::class, 'approve'])
            ->name('admin.reviews.approve');
        Route::delete('reviews/{review}/reject', [\App\Http\Controllers\API\Admin\AdminReviewController::class, 'reject'])
            ->name('admin.reviews.reject');
        Route::get('reviews/stats', [\App\Http\Controllers\API\Admin\AdminReviewController::class, 'stats'])
            ->name('admin.reviews.stats');
    });
});

// Public review statistics
Route::get('products/{product}/review-stats', [\App\Http\Controllers\API\ReviewController::class, 'stats'])
    ->name('products.reviews.stats');