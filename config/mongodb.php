<?php

return [
    'driver' => 'mongodb',
    'dsn' => env('MONGODB_URI', 'mongodb://localhost:27017'),
    'database' => env('MONGODB_DATABASE', 'gym_management'),
    'options' => [
        'database' => env('MONGODB_DATABASE', 'gym_management'),
    ],
    'migrations' => 'migrations',
    'retry_connect' => 3,
    'retry_interval' => 5,
];
