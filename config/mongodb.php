<?php

return [
    'default' => [
        'name' => 'mongodb',
        'driver' => 'mongodb',
        'host' => env('DB_HOST_MONGO', '127.0.0.1'),
        'port' => env('DB_PORT_MONGO', 27017),
        'database' => env('DB_DATABASE_MONGO', 'laravel'),
        'username' => env('DB_USERNAME_MONGO', ''),
        'password' => env('DB_PASSWORD_MONGO', ''),
        'options' => [
            'database' => env('DB_AUTHENTICATION_DATABASE', 'admin'),
        ],
    ],
];
