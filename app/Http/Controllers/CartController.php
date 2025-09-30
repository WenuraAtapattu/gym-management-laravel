<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the user's shopping cart
     */
    public function index()
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', Auth::id())
            ->firstOrCreate(['user_id' => Auth::id()]);

        return view('cart.index', [
            'cart' => $cart,
            'subtotal' => $this->calculateSubtotal($cart)
        ]);
    }

    /**
     * Add a product to the cart
     */
    public function store(Product $product, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        
        $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => $request->quantity]
        );

        return redirect()
            ->route('cart.index')
            ->with('success', 'Product added to cart');
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

        return redirect()
            ->route('cart.index')
            ->with('success', 'Cart updated');
    }

    /**
     * Remove an item from the cart
     */
    public function destroy(CartItem $cartItem)
    {
        $this->authorize('delete', $cartItem);
        
        $cartItem->delete();

        return redirect()
            ->route('cart.index')
            ->with('success', 'Item removed from cart');
    }

    /**
     * Clear the entire cart
     */
    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        
        if ($cart) {
            $cart->items()->delete();
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Cart cleared');
    }

    /**
     * Calculate the subtotal of the cart
     */
    private function calculateSubtotal($cart)
    {
        return $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }
}
