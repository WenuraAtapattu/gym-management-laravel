<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// MongoDB API Routes
Route::prefix('mongo')->name('mongo.')->group(function () {
    // Test MongoDB Connection
    Route::get('/test', [\App\Http\Controllers\TestMongoController::class, 'testConnection'])
        ->name('mongo.test.connection');

    // Public review routes (read-only)
    Route::get('products/{product}/reviews', [\App\Http\Controllers\API\MongoReviewController::class, 'index'])
        ->name('mongo.products.reviews.index');
    Route::get('products/{product}/reviews/{review}', [\App\Http\Controllers\API\MongoReviewController::class, 'show'])
        ->name('mongo.products.reviews.show');

    // Protected MongoDB routes
    Route::middleware('auth:sanctum')->group(function () {
        // Users
        Route::apiResource('users', \App\Http\Controllers\API\MongoUserController::class)
            ->names('mongo.users');

        // Products
        Route::apiResource('products', \App\Http\Controllers\API\MongoProductController::class)
            ->names('mongo.products');

        // Reviews CRUD
        Route::post('products/{product}/reviews', [\App\Http\Controllers\API\MongoReviewController::class, 'store'])
            ->name('mongo.products.reviews.store');
        Route::put('products/{product}/reviews/{review}', [\App\Http\Controllers\API\MongoReviewController::class, 'update'])
            ->name('mongo.products.reviews.update');
        Route::delete('products/{product}/reviews/{review}', [\App\Http\Controllers\API\MongoReviewController::class, 'destroy'])
            ->name('mongo.products.reviews.destroy');

        // Review stats
        Route::get('reviews/stats', [\App\Http\Controllers\API\MongoReviewController::class, 'stats'])
            ->name('mongo.reviews.stats');
        Route::get('products/{product}/reviews/stats', [\App\Http\Controllers\API\MongoReviewController::class, 'stats'])
            ->name('mongo.products.reviews.stats');
    });
});

// MySQL API Routes
Route::prefix('mysql')->name('mysql.')->group(function () {
    // Products
    Route::apiResource('products', \App\Http\Controllers\API\ProductController::class)
        ->names('products');

    // Reviews
    Route::prefix('products/{product}/reviews')->name('products.reviews.')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\ReviewController::class, 'index'])
            ->name('index');
        Route::get('/{review}', [\App\Http\Controllers\API\ReviewController::class, 'show'])
            ->name('show');
        Route::get('/stats', [\App\Http\Controllers\API\ReviewController::class, 'stats'])
            ->name('stats');

        // Protected routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [\App\Http\Controllers\API\ReviewController::class, 'store'])
                ->name('store');
            Route::put('/{review}', [\App\Http\Controllers\API\ReviewController::class, 'update'])
                ->name('update');
            Route::delete('/{review}', [\App\Http\Controllers\API\ReviewController::class, 'destroy'])
                ->name('destroy');
        });
    });
});

// Protected API Routes
Route::middleware('auth:sanctum')->group(function () {
    // Payments
    Route::apiResource('payments', \App\Http\Controllers\API\PaymentController::class);
    Route::get('payments/member/{user}', [\App\Http\Controllers\API\PaymentController::class, 'getMemberPayments'])
        ->name('payments.member');
    Route::get('payments/date-range', [\App\Http\Controllers\API\PaymentController::class, 'getPaymentsByDateRange'])
        ->name('payments.date-range');
    Route::post('payments/{payment}/status', [\App\Http\Controllers\API\PaymentController::class, 'updateStatus'])
        ->name('payments.update-status');

    // Cart
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\CartController::class, 'index'])
            ->name('index');
        Route::post('/items/{product}', [\App\Http\Controllers\API\CartController::class, 'store'])
            ->name('items.store');
        Route::put('/items/{cartItem}', [\App\Http\Controllers\API\CartController::class, 'update'])
            ->name('items.update');
        Route::delete('/items/{cartItem}', [\App\Http\Controllers\API\CartController::class, 'destroy'])
            ->name('items.destroy');
        Route::post('/clear', [\App\Http\Controllers\API\CartController::class, 'clear'])
            ->name('clear');
        Route::get('/count', [\App\Http\Controllers\API\CartController::class, 'count'])
            ->name('count');
    });

    // Report review
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
