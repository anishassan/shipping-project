<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shipping API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for shipping carrier APIs.
    | All sensitive information should be stored in environment variables.
    |
    */

    'carriers' => [
        'ups' => [
            'api_key' => env('UPS_API_KEY'),
            'api_secret' => env('UPS_API_SECRET'),
            'account_number' => env('UPS_ACCOUNT_NUMBER'),
            'test_mode' => env('UPS_TEST_MODE', true),
        ],
        'fedex' => [
            'api_key' => env('FEDEX_API_KEY'),
            'api_secret' => env('FEDEX_API_SECRET'),
            'account_number' => env('FEDEX_ACCOUNT_NUMBER'),
            'test_mode' => env('FEDEX_TEST_MODE', true),
        ],
        'dhl' => [
            'api_key' => env('DHL_API_KEY'),
            'api_secret' => env('DHL_API_SECRET'),
            'account_number' => env('DHL_ACCOUNT_NUMBER'),
            'test_mode' => env('DHL_TEST_MODE', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Configure security-related settings for the shipping module.
    |
    */

    'security' => [
        // Rate limiting settings
        'rate_limit' => [
            'enabled' => true,
            'max_attempts' => 60, // requests per minute
            'decay_minutes' => 1,
        ],

        // IP restrictions
        'ip_restrictions' => [
            'enabled' => false,
            'allowed_ips' => explode(',', env('SHIPPING_ALLOWED_IPS', '')),
        ],

        // API request timeout in seconds
        'timeout' => 30,

        // Maximum retry attempts for failed API calls
        'max_retries' => 3,

        // SSL verification
        'verify_ssl' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging settings for shipping operations.
    |
    */

    'logging' => [
        'enabled' => true,
        'channel' => env('SHIPPING_LOG_CHANNEL', 'daily'),
        'level' => env('SHIPPING_LOG_LEVEL', 'info'),
        'events' => [
            'label_generation' => true,
            'rate_calculation' => true,
            'tracking_updates' => true,
            'api_requests' => true,
            'errors' => true,
        ],
    ],
]; 