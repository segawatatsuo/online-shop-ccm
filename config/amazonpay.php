<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Amazon Pay Configuration
    |--------------------------------------------------------------------------
    |
    | Amazon Pay決済の設定
    |
    */

    'public_key_id' => env('AMAZON_PAY_PUBLIC_KEY_ID', 'SANDBOX-AFZNNIULXIPQCHDKP22S55W4'),
    
    'private_key_path' => env('AMAZON_PAY_PRIVATE_KEY_PATH', storage_path('app/keys/amazonpay_private.pem')),
    
    'region' => env('AMAZON_PAY_REGION', 'jp'),
    
    'sandbox' => env('AMAZON_PAY_SANDBOX', true),
    
    'store_id' => env('AMAZON_PAY_STORE_ID', 'amzn1.application-oa2-client.28e60372ee0d42c48e7c1c9036655abb'),
    
    'merchant_id' => env('AMAZON_PAY_MERCHANT_ID', 'A2MQAIFB5IWHUJ'),
    
    'store_name' => env('AMAZON_PAY_STORE_NAME', 'SHOP_NAME'),
    
    /*
    |--------------------------------------------------------------------------
    | Amazon Pay URLs
    |--------------------------------------------------------------------------
    */
    
    'checkout_js_url' => env('AMAZON_PAY_CHECKOUT_JS_URL', 'https://static-fe.payments-amazon.com/checkout.js'),
];