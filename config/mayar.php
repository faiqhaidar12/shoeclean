<?php

return [
    'api_key' => env('MAYAR_API_KEY'),
    'api_url' => env('MAYAR_API_URL', 'https://api.mayar.club'),
    'webhook_secret' => env('MAYAR_WEBHOOK_SECRET'),

    // Membership Product (SaaS subscription)
    'product_membership_id' => env('MAYAR_PRODUCT_MEMBERSHIP_ID'),
    'tier_pro_id' => env('MAYAR_TIER_PRO_ID'),
    'tier_business_id' => env('MAYAR_TIER_BUSINESS_ID'),

    // Top-up Product
    'product_topup_id' => env('MAYAR_PRODUCT_TOPUP_ID'),

    // Plans config
    'plans' => [
        'free' => [
            'name' => 'Basic (Free)',
            'order_limit' => 100,
            'max_outlets' => 1,
            'price' => 0,
        ],
        'pro' => [
            'name' => 'Pro',
            'order_limit' => null, // unlimited
            'max_outlets' => 1,
            'price' => 75000,
        ],
        'business' => [
            'name' => 'Business',
            'order_limit' => null, // unlimited
            'max_outlets' => null, // unlimited
            'price' => 200000,
        ],
    ],

    'topup' => [
        'quota' => 500,
        'price' => 100000,
    ],
];
