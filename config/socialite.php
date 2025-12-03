<?php

declare(strict_types=1);

return [
    'connections' => [
        'google' => [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect' => env('GOOGLE_REDIRECT_URL'),
        ],
        'microsoft' => [],
        'facebook' => [],
        'github' => [],
        'x' => [],
    ],
];
