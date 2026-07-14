<?php

return [
    'paths' => [
        'api/*',
        'company-profile',
        'bookings',
        'testimonials',
        'testimonials/*',
        'schedules/*',
        'user/bookings',
        'storage/*',
    ],
    'allowed_methods' => ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:5173'),
        env('FRONTEND_URL_ALT', 'http://127.0.0.1:5173'),
        'https://compro-rpnzl.vercel.app',
        'http://localhost:5174',  // Dev port alternative
        'http://127.0.0.1:5174',  // Dev port alternative
    ],
    'allowed_origins_patterns' => [
        '#^https://compro-rpnzl-[a-z0-9-]+\.vercel\.app$#',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
