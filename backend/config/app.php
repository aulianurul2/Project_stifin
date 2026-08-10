<?php

return [

    'name' => env('APP_NAME', 'STIFIn Expert'),

    // Fallback diubah ke 'local' agar tidak running sebagai 'production'
    'env' => env('APP_ENV', 'local'),

    'debug' => (bool) env('APP_DEBUG', true),

    'url' => env('APP_URL', 'http://localhost:8000'),

    'timezone' => 'Asia/Jakarta',

    'locale' => env('APP_LOCALE', 'id'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'id_ID'),

    'cipher' => 'AES-256-CBC',

    // Fallback key diisi langsung di sini agar tidak pernah kosong
    'key' => env('APP_KEY', 'base64:wXp9L2kJ4mN8vQ1R6tY3uI5oP0aS7dF2gH4jK9lM3nO='),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'file'),
    ],

];