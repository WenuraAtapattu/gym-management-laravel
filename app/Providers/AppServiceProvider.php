<?php

namespace App\Providers;

use App\Http\Middleware\MergeGuestCart;
use App\Models\MongoUser;
use App\Models\MongoReview;
use App\Observers\MongoReviewObserver;
use App\Observers\MongoUserObserver;
use App\Services\CartService;
use App\View\Components\ActionMessage;
use App\View\Components\Button;
use App\View\Components\DangerButton;
use App\View\Components\DialogModal;
use App\View\Components\InputError;
use App\View\Components\Label;
use App\View\Components\Modal;
use App\View\Components\SecondaryButton;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     */
    public function register()
    {
        // Register component aliases
        Blade::component('jet-action-message', ActionMessage::class);
        Blade::component('jet-button', Button::class);
        Blade::component('jet-danger-button', DangerButton::class);
        Blade::component('jet-dialog-modal', DialogModal::class);
        Blade::component('jet-input-error', InputError::class);
        Blade::component('jet-label', Label::class);
        Blade::component('jet-modal', Modal::class);
        Blade::component('jet-secondary-button', SecondaryButton::class);
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
