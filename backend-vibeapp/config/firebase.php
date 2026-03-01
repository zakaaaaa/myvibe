<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Credentials file path used for accessing Firebase services.
    |
    */

    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', storage_path('firebase/firebase_credentials.json')),

        'auto_discovery' => env('FIREBASE_AUTO_DISCOVERY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Realtime Database
    |--------------------------------------------------------------------------
    */

    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Dynamic Links
    |--------------------------------------------------------------------------
    */

    'dynamic_links' => [
        'default_domain' => env('FIREBASE_DYNAMIC_LINKS_DEFAULT_DOMAIN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Storage
    |--------------------------------------------------------------------------
    */

    'storage' => [
        'default_bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET'),
    ],
];
