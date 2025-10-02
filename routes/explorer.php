<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DataExplorerController;
use App\Http\Controllers\Admin\MongoDBExplorerController;

// SQL Data Explorer
Route::middleware(['auth', 'verified'])->prefix('admin/data-explorer')->name('admin.data-explorer.')->group(function () {
    Route::get('/', [DataExplorerController::class, 'index'])->name('index');
    Route::get('/products', [DataExplorerController::class, 'products'])->name('products');
    Route::get('/users', [DataExplorerController::class, 'users'])->name('users');
    Route::get('/orders', [DataExplorerController::class, 'orders'])->name('orders');
    Route::get('/carts', [DataExplorerController::class, 'carts'])->name('carts');
});

// MongoDB Explorer
Route::middleware(['auth', 'verified'])->prefix('admin/mongodb-explorer')->name('admin.mongodb-explorer.')->group(function () {
    Route::get('/', [MongoDBExplorerController::class, 'index'])->name('index');
    Route::post('/query', [MongoDBExplorerController::class, 'query'])->name('query');
    Route::get('/document/{id}', [MongoDBExplorerController::class, 'document'])->name('document');
});
