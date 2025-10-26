<?php

return [

    'facebook' => [
        'client_id'            => env('FACEBOOK_CLIENT_ID'),
        'client_secret'        => env('FACEBOOK_APP_SECRET'),
        'redirect'             => env('FACEBOOK_REDIRECT_URI'),
        'webhook_verify_token' => env('WEBHOOK_VERIFY_TOKEN', 'dev_verify_token'),
        'app_secret'           => env('FACEBOOK_APP_SECRET'), // used for signature verify
    ],
];
