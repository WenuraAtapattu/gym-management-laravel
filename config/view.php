<?php

return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

    'expires' => env('VIEW_CHECK_EXPIRATION', true) ? (int) env('VIEW_CHECK_EXPIRATION', 30) : false,
];
