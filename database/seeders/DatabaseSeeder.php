<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user using the dedicated seeder
        $this->call([
            AdminUserSeeder::class,
            InstructorSeeder::class,
            FitnessClassSeeder::class,
            MembershipSeeder::class,
            CartSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class, // Add ProductSeeder here
        ]);

        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create placeholder images directory if it doesn't exist
        $productsPath = storage_path('app/public/products');
        if (!file_exists($productsPath)) {
            mkdir($productsPath, 0777, true);
        }
        
        // Create empty placeholder files
        $placeholderImages = ['treadmill.jpg', 'dumbbells.jpg', 'yoga-mat.jpg', 'whey-protein.jpg', 
                            'dumbbell-set.jpg', 'training-gloves.jpg', 'fitness-tracker.jpg', 
                            'workout-leggings.jpg', 'resistance-bands.jpg', 'gym-bag.jpg'];
        foreach ($placeholderImages as $image) {
            $path = $productsPath . '/' . $image;
            if (!file_exists($path)) {
                file_put_contents($path, '');
            }
        }
    }
}
