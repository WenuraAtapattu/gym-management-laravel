<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Review;
use App\Policies\CartItemPolicy;
use App\Policies\CartPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
        Review::class => ReviewPolicy::class,
        Cart::class => CartPolicy::class,
        CartItem::class => CartItemPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('view-order', function ($user, Order $order) {
            return $user->id === $order->user_id || $user->is_admin;
        });

        Gate::define('update-order', function ($user, Order $order) {
            return $user->id === $order->user_id || $user->is_admin;
        });
        
        Gate::define('admin', function ($user) {
            return $user->is_admin;
        });

        // Add the merge.cart middleware to the authenticated session group
        Route::middleware(['auth', 'merge.cart'])->group(function () {
            // Your authenticated routes will go here
        });
    }
}
