<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MongoDB\Client as MongoClient;

class MongoDBServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mongodb', function ($app) {
            $config = $app->make('config');
            
            $uri = 'mongodb+srv://heroku_user:798200305v@cluster0.qi715iy.mongodb.net/laravel_sem2?retryWrites=true&w=majority';
            
            return new MongoClient($uri, [
                'tls' => true,
                'authSource' => 'admin',
                'retryWrites' => true,
                'w' => 'majority',
                'serverSelectionTimeoutMS' => 10000,
                'socketTimeoutMS' => 45000,
                'connectTimeoutMS' => 10000,
            ]);
        });
    }

    public function boot()
    {
        //
    }
}
