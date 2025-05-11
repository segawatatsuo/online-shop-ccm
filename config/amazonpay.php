<?php

return [
    'sandbox' => true,
    'store_id' => env('AMAZON_PAY_STORE_ID'),
    'public_key_id' => env('AMAZON_PAY_PUBLIC_KEY_ID'),
    'private_key' => storage_path('amazonpay/private.key'),
    'region' => 'jp',
];
