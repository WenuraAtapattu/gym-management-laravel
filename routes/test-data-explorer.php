<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Cart;

Route::get('/test-data-explorer', function () {
    // Test data retrieval
    try {
        $data = [
            'products_count' => Product::count(),
            'users_count' => User::count(),
            'orders_count' => Order::count(),
            'carts_count' => Cart::count(),
            'sample_products' => Product::with('category')->take(2)->get()->toArray(),
            'sample_users' => User::withCount('orders')->take(2)->get()->toArray(),
            'sample_orders' => Order::with(['user', 'items'])->take(2)->get()->toArray(),
            'sample_carts' => Cart::with(['user', 'items'])->take(2)->get()->toArray(),
        ];
        
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
