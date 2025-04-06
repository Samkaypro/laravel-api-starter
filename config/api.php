<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Versioning
    |--------------------------------------------------------------------------
    |
    | This value determines the latest and current API version.
    | When a version becomes deprecated, add it to the deprecated_versions array.
    |
    */
    'latest_version' => 'v1',
    'deprecated_versions' => [
        // No deprecated versions yet
    ],

    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting
    |--------------------------------------------------------------------------
    |
    | These values determine the rate limits for the API.
    |
    */
    'rate_limits' => [
        'default' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'authenticated' => [
            'max_attempts' => 120,
            'decay_minutes' => 1,
        ],
        'admin' => [
            'max_attempts' => 500,
            'decay_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Documentation
    |--------------------------------------------------------------------------
    |
    | Configuration for API documentation using L5-Swagger
    |
    */
    'documentation' => [
        'title' => 'Laravel API Starter',
        'description' => 'A Laravel API Starter Kit with authentication, RBAC, versioning, and rate-limiting.',
        'version' => '1.0.0',
        'contact' => [
            'name' => 'API Support',
            'email' => 'api@example.com',
        ],
    ],
]; 