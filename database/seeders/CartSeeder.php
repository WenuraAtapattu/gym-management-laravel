<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to avoid constraint issues
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Clear existing data
        DB::table('cart_items')->truncate();
        DB::table('carts')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Get some products
        $products = Product::inRandomOrder()->take(5)->get();
        
        if ($products->isEmpty()) {
            $this->command->info('No products found. Please run ProductSeeder first.');
            return;
        }
        
        // Create a cart for the first user
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }
        
        // Create a cart for the user
        $cart = Cart::create([
            'user_id' => $user->id,
        ]);
        
        // Add some items to the cart
        foreach ($products as $index => $product) {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $index + 1, // Just for variety
            ]);
        }
        
        $this->command->info('Cart seeder completed successfully!');
        $this->command->info("Created cart for user: {$user->name} with {$products->count()} items.");
    }
}
