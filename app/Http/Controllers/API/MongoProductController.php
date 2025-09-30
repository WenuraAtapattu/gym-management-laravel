<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MongoProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MongoProductController extends Controller
{
    public function index()
    {
        $products = MongoProduct::all();
        
        return response()->json([
            'data' => $products,
            'message' => 'Products retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = MongoProduct::create($request->all());
        
        return response()->json([
            'data' => $product,
            'message' => 'Product created successfully'
        ], 201);
    }

    public function show($id)
    {
        $product = MongoProduct::findOrFail($id);
        
        return response()->json([
            'data' => $product,
            'message' => 'Product retrieved successfully'
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = MongoProduct::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($request->all());
        
        return response()->json([
            'data' => $product,
            'message' => 'Product updated successfully'
        ]);
    }

    public function destroy($id)
    {
        try {
            $product = MongoProduct::findOrFail($id);
            $product->delete();
            
            return response()->json([
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}