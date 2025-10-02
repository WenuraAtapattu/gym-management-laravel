<?php

return [
    'default' => [
        'name' => 'mongodb',
        'driver' => 'mongodb',
        'dsn' => env('MONGODB_URI', 'mongodb://127.0.0.1:27017'),
        'database' => env('MONGODB_DATABASE', 'gym_management'),
        'options' => [
            'retryWrites' => true,
            'w' => 'majority',
            'ssl' => false,
        ],
    ],
];
