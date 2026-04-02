<?php

return [
    'merchant_code' => env('DUITKU_MERCHANT_CODE'),
    'api_key' => env('DUITKU_API_KEY'),
    'sandbox' => (bool) env('DUITKU_SANDBOX', true),
    'default_payment_method' => env('DUITKU_DEFAULT_PAYMENT_METHOD'),
    'preferred_payment_methods' => env('DUITKU_PREFERRED_PAYMENT_METHODS', ''),
    'expiry_period' => (int) env('DUITKU_EXPIRY_PERIOD', 60),
    'callback_url' => env('DUITKU_CALLBACK_URL', rtrim(env('APP_URL', ''), '/') . '/webhook/duitku'),
];
