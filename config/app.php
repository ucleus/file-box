<?php

return [
    'app_name' => 'Ucleus Logo Delivery',
    'studio_name' => 'Ucleus',
    'studio_email' => 'admin@ucleus.com',
    'base_url' => getenv('BASE_URL') ?: 'http://localhost',

    // Database
    'db_path' => __DIR__ . '/../database/app.db',

    // Storage paths
    'storage_path' => __DIR__ . '/../storage',
    'deliveries_path' => __DIR__ . '/../storage/deliveries',
    'zips_path' => __DIR__ . '/../storage/zips',

    // Upload settings
    'max_upload_size' => 50 * 1024 * 1024, // 50MB
    'allowed_extensions' => ['png', 'jpg', 'jpeg', 'svg', 'pdf', 'ai', 'eps', 'zip'],
    'preview_extensions' => ['png', 'jpg', 'jpeg', 'svg', 'pdf'],

    // Brand colors
    'brand_colors' => [
        'primary' => '#450693',
        'secondary' => '#8C00FF',
        'accent1' => '#FF3F7F',
        'accent2' => '#FFC400',
    ],

    // Session
    'session_lifetime' => 7200, // 2 hours

    // Pagination
    'per_page' => 20,
];
