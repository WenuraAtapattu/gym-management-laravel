<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        return view('checkout.index', [
            'cart' => $cart,
            'subtotal' => $cart->items->sum(function ($item) {
                return $item->quantity * $item->product->price;
            })
        ]);
    }

    public function store(Request $request)
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|in:credit_card,debit_card,paypal'
        ]);

        return DB::transaction(function () use ($request, $cart) {
            // Format shipping address as JSON
            $shippingAddress = [
                'address' => $request->shipping_address,
                'type' => 'shipping',
                'created_at' => now()->toDateTimeString()
            ];

            // Calculate total amount
            $totalAmount = $cart->items->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . time() . '-' . strtoupper(substr(uniqid(), -6)),
                'status' => 'pending',
                'shipping_address' => json_encode($shippingAddress),
                'payment_method' => $request->payment_method,
                'subtotal' => $totalAmount,
                'total' => $totalAmount,
                'customer_email' => Auth::user()->email,
                'payment_status' => 'pending'
            ]);

            // Create order items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'subtotal' => $item->quantity * $item->product->price
                ]);

                // Update product stock
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // Clear the cart
            $cart->items()->delete();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');
        });
    }
}