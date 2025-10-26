<?php
return [
    'default' => env('LOG_CHANNEL', 'stack'),
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'fbwebhook'],
            'ignore_exceptions' => false,
        ],
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],
        'fbwebhook' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/fbwebhook.log'),
            'level'  => env('LOG_LEVEL', 'debug'),
            'days'   => 14,
        ],
    ],
];
