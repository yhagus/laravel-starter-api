<?php

declare(strict_types=1);

return [
    'connections' => [
        'google' => [
            'client_id' => env('GOOGLE_CLIENT_ID', null),
            'client_secret' => env('GOOGLE_CLIENT_SECRET', null),
            'redirect' => env('GOOGLE_REDIRECT_URL', null),
        ],
    ],
];
