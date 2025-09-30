<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Get the total number of items in the user's cart
     */
    public static function getCartItemCount(): int
    {
        if (!Auth::check()) {
            return 0;
        }

        $cart = Cart::where('user_id', Auth::id())->first();
        
        if (!$cart) {
            return 0;
        }

        return $cart->items()->sum('quantity');
    }
}