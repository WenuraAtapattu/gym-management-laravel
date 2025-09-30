<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Premium Adjustable Dumbbell Set',
                'brand' => 'PowerGym',
                'description' => 'High-quality adjustable dumbbell set with 5-50 lbs weight range. Perfect for home workouts and strength training.',
                'price' => 299.99,
                'compare_at_price' => 349.99,
                'stock_quantity' => 50,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_new_arrival' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Professional Yoga Mat',
                'brand' => 'FlexiMat',
                'description' => 'Extra thick 1/2 inch non-slip yoga mat with carrying strap. Ideal for all types of yoga and floor exercises.',
                'price' => 59.99,
                'compare_at_price' => 79.99,
                'stock_quantity' => 100,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_new_arrival' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Resistance Bands Set',
                'brand' => 'FitFlex',
                'description' => '5-piece resistance bands set with door anchor and exercise guide. Perfect for full-body workouts at home or gym.',
                'price' => 29.99,
                'compare_at_price' => 39.99,
                'stock_quantity' => 75,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_new_arrival' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Foam Roller',
                'brand' => 'RecoveryPro',
                'description' => 'High-density foam roller for muscle recovery and myofascial release. 36 inches long for full-body use.',
                'price' => 34.99,
                'compare_at_price' => 44.99,
                'stock_quantity' => 60,
                'is_featured' => false,
                'is_bestseller' => true,
                'is_new_arrival' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Jump Rope with Digital Counter',
                'brand' => 'SpeedRope',
                'description' => 'Professional speed jump rope with digital counter and adjustable length. Perfect for cardio and HIIT workouts.',
                'price' => 19.99,
                'compare_at_price' => 24.99,
                'stock_quantity' => 120,
                'is_featured' => true,
                'is_bestseller' => false,
                'is_new_arrival' => true,
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->name = $productData['name'];
            $product->brand = $productData['brand'];
            $product->description = $productData['description'];
            $product->price = $productData['price'];
            $product->compare_at_price = $productData['compare_at_price'];
            $product->stock_quantity = $productData['stock_quantity'];
            $product->is_featured = $productData['is_featured'];
            $product->is_bestseller = $productData['is_bestseller'];
            $product->is_new_arrival = $productData['is_new_arrival'];
            $product->is_active = $productData['is_active'];
            $product->slug = Str::slug($productData['name']);
            
            // Generate a simple barcode for demo purposes
            $product->barcode = 'PRD' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            
            $product->save();
        }
    }
}
