<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        '*', // Allow all origins for testing (remove in production)
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost',
        'http://127.0.0.1',
        'capacitor://localhost',
        'ionic://localhost',
        'http://192.168.18.36:5173', // Allow LAN IP for dev
        'http://192.168.18.36',      // Allow LAN IP for mobile
        'http://192.168.18.36:9001', // Allow backend port for mobile
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
