<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;  // Added missing import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;  // Added for better auth handling

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only(['profile', 'updateProfile']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->take(8)
            ->get();

        $user = Auth::user();
        $userStats = [
            'total_orders' => 0,
            'recent_orders' => collect(),
            'favorite_products' => collect()
        ];

        if ($user) {
            $userStats = [
                'total_orders' => $user->orders()->count(),
                'recent_orders' => $user->orders()
                    ->with(['items.product'])
                    ->latest()
                    ->take(3)
                    ->get(),
                'favorite_products' => $user->orders()
                    ->with(['items.product'])
                    ->get()
                    ->pluck('items')
                    ->flatten()
                    ->pluck('product')
                    ->filter()
                    ->unique('id')
                    ->take(4)
            ];
        }

        return view('home', [
            'featuredProducts' => $featuredProducts,
            'userStats' => $userStats,
            'user' => $user
        ]);
    }

    /**
     * Show the user profile.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        $orders = $user->orders()
            ->with(['items.product'])
            ->latest()
            ->get();
        
        return view('profile.show', compact('user', 'orders'));
    }

    /**
     * Update the user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.Auth::id(),
            'phone' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
        ]);

        Auth::user()->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }
}