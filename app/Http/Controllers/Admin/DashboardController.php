<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalProducts' => Product::count(),
            'totalUsers' => User::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)->sum('total'),
            'recentOrders' => Order::with(['user', 'items'])
                ->latest()
                ->take(5)
                ->get(),
            'popularProducts' => \DB::table('order_items')
                ->select('products.name', \DB::raw('SUM(order_items.quantity) as total_quantity'))
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_quantity', 'desc')
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', $data);
    }

    public function products()
    {
        $products = Product::with('category')
            ->latest()
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function users()
    {
        $users = User::withCount(['orders'])
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function orders()
    {
        $orders = Order::with(['user', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function carts()
    {
        $carts = Cart::with(['user', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('admin.carts.index', compact('carts'));
    }
}
