<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Configuration
    |--------------------------------------------------------------------------
    */

    'grace_period_days' => env('SUBSCRIPTION_GRACE_PERIOD_DAYS', 7),
    'trial_notification_days' => env('SUBSCRIPTION_TRIAL_NOTIFICATION_DAYS', 3),
    'invoice_due_days' => env('SUBSCRIPTION_INVOICE_DUE_DAYS', 14),
    'tax_rate' => env('SUBSCRIPTION_TAX_RATE', 0.10),

    'payment_gateways' => [
        'stripe' => env('PAYMENT_STRIPE_ENABLED', true),
        'razorpay' => env('PAYMENT_RAZORPAY_ENABLED', false),
    ],

    'email_notifications' => [
        'send_welcome_email' => env('SUBSCRIPTION_SEND_WELCOME_EMAIL', true),
        'send_expiration_reminder' => env('SUBSCRIPTION_SEND_EXPIRATION_REMINDER', true),
        'send_trial_ending_reminder' => env('SUBSCRIPTION_SEND_TRIAL_ENDING_REMINDER', true),
        'send_invoice_email' => env('SUBSCRIPTION_SEND_INVOICE_EMAIL', true),
    ],

    'cache' => [
        'enabled' => env('SUBSCRIPTION_CACHE_ENABLED', true),
        'ttl_minutes' => env('SUBSCRIPTION_CACHE_TTL', 60),
    ],

    'limits' => [
        'max_downgrade_days' => env('SUBSCRIPTION_MAX_DOWNGRADE_DAYS', 14),
        'min_billing_amount' => env('SUBSCRIPTION_MIN_BILLING_AMOUNT', 0.01),
    ],
];
