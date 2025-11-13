<?php

return [
    // Token settings
    'token_length' => 32,
    'token_bytes' => 24, // Will generate 32 char base64url token

    // Rate limiting
    'rate_limits' => [
        'download' => [
            'max_attempts' => 100,
            'window_minutes' => 60,
        ],
        'otp_request' => [
            'max_attempts' => 5,
            'window_minutes' => 15,
        ],
        'otp_verify' => [
            'max_attempts' => 5,
            'window_minutes' => 15,
        ],
        'page_view' => [
            'max_attempts' => 200,
            'window_minutes' => 60,
        ],
    ],

    // Security headers
    'headers' => [
        'X-Frame-Options' => 'DENY',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
    ],

    // Audit log retention (days)
    'log_retention_days' => 90,
];
