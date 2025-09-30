<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ReviewService;

class ReviewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/reviews.php', 'reviews'
        );

        $this->app->singleton(ReviewService::class, function ($app) {
            return new ReviewService();
        });

        $this->app->alias(ReviewService::class, 'reviews');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../../config/reviews.php' => config_path('reviews.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../../database/migrations/2025_10_01_000001_create_reviews_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_reviews_table.php'),
            __DIR__.'/../../database/migrations/2025_10_01_000002_create_reports_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 1).'_create_reports_table.php'),
        ], 'migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Add any review-related console commands here
            ]);
        }
    }
}
