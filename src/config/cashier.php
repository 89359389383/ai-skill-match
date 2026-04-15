<?php

return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],
    'currency' => env('CASHIER_CURRENCY', 'jpy'),
    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'ja_JP'),
];
