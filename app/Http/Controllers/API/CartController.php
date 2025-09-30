<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get the user's cart
     */
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        return response()->json([
            'cart' => $cart->load('items.product')
        ]);
    }

    /**
     * Add an item to the cart
     */
    public function store(Product $product, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        
        $cartItem = $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => $request->quantity]
        );

        return response()->json([
            'message' => 'Product added to cart',
            'cart_item' => $cartItem->load('product')
        ], 201);
    }

    /**
     * Update a cart item's quantity
     */
    public function update(CartItem $cartItem, Request $request)
    {
        $this->authorize('update', $cartItem);
        
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'message' => 'Cart item updated',
            'cart_item' => $cartItem->load('product')
        ]);
    }

    /**
     * Remove an item from the cart
     */
    public function destroy(CartItem $cartItem)
    {
        $this->authorize('delete', $cartItem);
        
        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart'
        ]);
    }

    /**
     * Clear the cart
     */
    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        
        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'message' => 'Cart cleared'
        ]);
    }

    /**
     * Get the number of items in the cart
     */
    public function count()
    {
        $count = 0;
        $cart = Cart::where('user_id', Auth::id())->first();
        
        if ($cart) {
            $count = $cart->items()->sum('quantity');
        }

        return response()->json([
            'count' => $count
        ]);
    }
}
