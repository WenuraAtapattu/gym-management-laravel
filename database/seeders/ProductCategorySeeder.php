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
            [
                'name' => 'Protein', 
                'slug' => 'protein', 
                'description' => 'Whey protein, casein, and plant-based protein supplements'
            ],
            [
                'name' => 'Pre-Workout', 
                'slug' => 'pre-workout', 
                'description' => 'Energy and endurance boosters for your workout sessions'
            ],
            [
                'name' => 'Vitamins & Health', 
                'slug' => 'vitamins-health', 
                'description' => 'Essential vitamins and health supplements'
            ],
            [
                'name' => 'Weight Management', 
                'slug' => 'weight-management', 
                'description' => 'Supplements to support weight loss and muscle gain'
            ],
            [
                'name' => 'Strength Equipment', 
                'slug' => 'strength-equipment', 
                'description' => 'Dumbbells, barbells, and strength training gear'
            ],
            [
                'name' => 'Cardio Machines', 
                'slug' => 'cardio-machines', 
                'description' => 'Treadmills, ellipticals, and other cardio equipment'
            ],
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
                'description' => 'Premium quality whey protein isolate with 90% protein content',
                'price' => 49.99,
                'stock_quantity' => 100,
                'category_id' => $categoryIds['protein'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Pre-Workout Energizer',
                'description' => 'Powerful pre-workout formula with beta-alanine and caffeine',
                'price' => 34.99,
                'stock_quantity' => 75,
                'category_id' => $categoryIds['pre-workout'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Adjustable Dumbbell Set',
                'description' => 'Space-saving adjustable dumbbell set 5-25kg',
                'price' => 199.99,
                'stock_quantity' => 30,
                'category_id' => $categoryIds['strength-equipment'],
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
                'category_id' => $categoryIds['accessories'],
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
