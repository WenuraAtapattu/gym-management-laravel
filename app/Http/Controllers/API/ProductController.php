<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseMongoController;
use App\Models\Product; // Your MongoDB model

class ProductController extends BaseMongoController
{
    public function index()
    {
        try {
            $products = Product::all();
            return response()->json($products);
        } catch (\Exception $e) {
            \Log::error('Error fetching products: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    
    // Add other API methods as needed
}
