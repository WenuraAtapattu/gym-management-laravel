<?php

namespace Database\Seeders;

use App\Models\MongoReview;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MongoDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create regular user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Create some test products if none exist
        if (Product::count() === 0) {
            Product::factory()->count(5)->create();
        }

        // Create some test reviews
        $products = Product::all();
        
        // Create approved reviews
        MongoReview::factory()
            ->count(20)
            ->approved()
            ->sequence(fn ($sequence) => [
                'reviewable_id' => $products->random()->id,
                'user_id' => $user->id,
            ])
            ->create();

        // Create pending reviews
        MongoReview::factory()
            ->count(5)
            ->pending()
            ->sequence(fn ($sequence) => [
                'reviewable_id' => $products->random()->id,
                'user_id' => $user->id,
            ])
            ->create();

        // Create some guest reviews
        MongoReview::factory()
            ->count(10)
            ->guest()
            ->approved()
            ->sequence(fn ($sequence) => [
                'reviewable_id' => $products->random()->id,
            ])
            ->create();
    }
}
