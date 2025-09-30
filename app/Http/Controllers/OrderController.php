<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        return view('orders.show', [
            'order' => $order->load(['items.product', 'shipping'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string|max:500',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Start transaction
        return DB::transaction(function () use ($request, $product) {
            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $product->price * $request->quantity,
                'total' => $product->price * $request->quantity,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
            ]);

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);

            // Update product stock
            $product->decrement('stock', $request->quantity);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');
        });
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled'
        ]);

        $order->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        // If order is cancelled, restore product stock
        if ($request->status === 'cancelled') {
            foreach ($order->items as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
            }
        }

        return back()->with('success', 'Order status updated successfully');
    }
}
