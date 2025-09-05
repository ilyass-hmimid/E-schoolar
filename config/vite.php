<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vite Server Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the Vite development server URL and build path.
    | These settings determine where your assets will be loaded from in
    | development and production environments.
    |
    */

    'paths' => [
        'resources/css/app.css',
        'resources/css/adminlte-custom.css',
        'resources/js/app.js',
    ],

    'server' => [
        'host' => env('VITE_APP_HOST', '127.0.0.1'),
        'port' => env('VITE_APP_PORT', 5173),
        'hmr' => env('VITE_DEV_SERVER_URL') ? [
            'host' => '127.0.0.1',
            'protocol' => 'ws',
            'port' => env('VITE_APP_PORT', 5173),
        ] : null,
    ],

    'build_path' => 'public/build',
];
