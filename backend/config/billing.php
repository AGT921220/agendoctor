<?php

return [
    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

        // JSON: {"BASIC":"price_...","PRO":"price_..."}
        'price_ids' => json_decode(env('STRIPE_PRICE_IDS', '{}'), true) ?: [],
    ],

    // URLs usadas por Checkout/Portal.
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),
];

