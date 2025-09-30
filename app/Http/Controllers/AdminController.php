<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        // Remove the auth middleware since it's already applied in the route group
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Current period stats
        $currentPeriodRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonth())
            ->sum('total');
            
        // Previous period stats
        $previousPeriodRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonths(2), now()->subMonth()])
            ->sum('total');
            
        // Calculate percentage change
        $revenueChange = 0;
        if ($previousPeriodRevenue > 0) {
            $revenueChange = (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100;
        } elseif ($currentPeriodRevenue > 0) {
            $revenueChange = 100; // 100% increase from 0
        }

        $stats = [
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_customers' => User::where('is_admin', false)->count(),
            'revenue' => Order::where('status', 'completed')->sum('total'),
            'revenue_change' => round($revenueChange, 1),
        ];

        $recentOrders = Order::with(['user', 'items.product'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders
        ]);
    }

    public function orders()
    {
        $orders = Order::with(['user', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', [
            'orders' => $orders
        ]);
    }

    public function orderDetails(Order $order)
    {
        $order->load(['user', 'items.product', 'shipping']);
        
        return view('admin.orders.show', [
            'order' => $order
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated successfully!');
    }

    public function users()
    {
        $users = User::where('is_admin', false)
            ->latest()
            ->paginate(15);

        return view('admin.users.index', [
            'users' => $users
        ]);
    }

    public function deleteUser(User $user)
    {
        DB::beginTransaction();
        
        try {
            // Delete user's orders and related data
            $user->orders()->each(function($order) {
                $order->items()->delete();
                $order->delete();
            });

            // Delete user
            $user->delete();

            DB::commit();
            
            return back()->with('success', 'User deleted successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete user. Please try again.');
        }
    }
    
    /**
     * Show the form for editing the specified user.
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'is_admin' => 'sometimes|boolean'
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }
}
