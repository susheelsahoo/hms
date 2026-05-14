<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HMS Modules
    |--------------------------------------------------------------------------
    |
    | Each enabled module can expose its own service provider, API routes,
    | migrations, policies, events, jobs, DTOs, and tests while staying inside
    | a single deployable Laravel application.
    |
    */

    'enabled' => [
        'Auth',
        'Organization',
        'User',
        'Hotel',
        'Room',
        'Guest',
        'Booking',
        'Payment',
        'Invoice',
        'Notification',
        'Report',
        'Subscription',
        'GlobalAnalytics',
        'Audit',
        'Shared',
    ],

    'base_path' => base_path('Modules'),

    'api_prefix' => 'api/v1',
];
