<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataExplorerController extends Controller
{
    public function index()
    {
        return view('admin.data-explorer.index');
    }

    public function carts()
    {
        $carts = Cart::with(['user', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('admin.data-explorer.carts', compact('carts'));
    }

    public function products()
    {
        $products = Product::with('category')
            ->latest()
            ->paginate(15);

        return view('admin.data-explorer.products', compact('products'));
    }

    public function users()
    {
        $users = User::withCount(['orders'])
            ->latest()
            ->paginate(15);

        return view('admin.data-explorer.users', compact('users'));
    }

    public function orders()
    {
        $orders = Order::with(['user', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('admin.data-explorer.orders', compact('orders'));
    }
}
