<?php

namespace App\Providers;

use App\Http\Middleware\MergeGuestCart;
use App\Models\MongoReview;
use App\Models\MongoUser;
use App\Observers\MongoReviewObserver;
use App\Observers\MongoUserObserver;
use Illuminate\Support\Facades\Schema;
use App\Services\CartService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL
        Schema::defaultStringLength(191);
        
        Route::aliasMiddleware('merge.cart', MergeGuestCart::class);
        
        // Register model observers if MongoDB is configured
        if (class_exists(MongoUser::class) && class_exists(MongoUserObserver::class)) {
            MongoUser::observe(MongoUserObserver::class);
        }
        
        if (class_exists(MongoReview::class) && class_exists(MongoReviewObserver::class)) {
            MongoReview::observe(MongoReviewObserver::class);
        }
        View::composer('profile.show', function ($view) {
            $user = Auth::user();
            if ($user && !$view->offsetExists('orders')) {
                $orders = $user->orders()
                    ->with(['items.product'])
                    ->latest()
                    ->get();
                $view->with('orders', $orders);
            }
        });
    }
}
