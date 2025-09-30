<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data in the correct order to respect foreign key constraints
        DB::table('order_items')->truncate();
        DB::table('cart_items')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create categories
        $categories = [
            ['name' => 'Supplements', 'slug' => 'supplements', 'description' => 'Performance enhancing supplements for your fitness journey'],
            ['name' => 'Equipment', 'slug' => 'equipment', 'description' => 'High-quality gym equipment for home and professional use'],
            ['name' => 'Apparel', 'slug' => 'apparel', 'description' => 'Comfortable and stylish workout clothing'],
            ['name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Essential fitness accessories'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Get all categories
        $categoryIds = Category::pluck('id', 'slug');

        // Sample products
        $products = [
            [
                'name' => 'Whey Protein Isolate',
                'slug' => 'whey-protein-isolate',
                'description' => 'Premium quality whey protein isolate with 25g of protein per serving',
                'price' => 49.99,
                'image' => 'products/whey-protein.jpg',
                'stock_quantity' => 100,
                'category_id' => $categoryIds['supplements'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Premium Dumbbell Set',
                'slug' => 'premium-dumbbell-set',
                'description' => 'Set of high-quality rubber hex dumbbells for home gym',
                'price' => 199.99,
                'image' => 'products/dumbbell-set.jpg',
                'stock_quantity' => 50,
                'category_id' => $categoryIds['equipment'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Yoga Mat',
                'slug' => 'yoga-mat',
                'description' => 'Eco-friendly non-slip yoga mat for all types of workouts',
                'price' => 29.99,
                'image' => 'products/yoga-mat.jpg',
                'stock_quantity' => 75,
                'category_id' => $categoryIds['equipment'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Training Gloves',
                'slug' => 'training-gloves',
                'description' => 'Padded weight lifting gloves for better grip and protection',
                'price' => 24.99,
                'image' => 'products/training-gloves.jpg',
                'stock_quantity' => 75,
                'category_id' => $categoryIds['accessories'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Fitness Tracker',
                'slug' => 'fitness-tracker',
                'description' => 'Advanced fitness tracker with heart rate monitoring',
                'price' => 89.99,
                'image' => 'products/fitness-tracker.jpg',
                'stock_quantity' => 30,
                'category_id' => $categoryIds['accessories'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Workout Leggings',
                'slug' => 'workout-leggings',
                'description' => 'High-waisted compression leggings for all types of workouts',
                'price' => 39.99,
                'image' => 'products/workout-leggings.jpg',
                'stock_quantity' => 60,
                'category_id' => $categoryIds['apparel'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Resistance Bands Set',
                'slug' => 'resistance-bands-set',
                'description' => 'Set of 5 resistance bands with different tension levels',
                'price' => 34.99,
                'image' => 'products/resistance-bands.jpg',
                'stock_quantity' => 60,
                'category_id' => $categoryIds['equipment'],
                'is_featured' => true,
                'is_active' => true
            ],
            [
                'name' => 'Gym Bag',
                'slug' => 'gym-bag',
                'description' => 'Spacious gym bag with separate shoe compartment',
                'price' => 44.99,
                'image' => 'products/gym-bag.jpg',
                'stock_quantity' => 35,
                'category_id' => $categoryIds['accessories'],
                'is_featured' => false,
                'is_active' => true
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
