<?php

return [
    'b2c' => [
        'env' => env('MPESA_ENV', 'sandbox'),
        'consumer_key' => env('MPESA_B2C_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_B2C_CONSUMER_SECRET'),
        'initiator_name' => env('MPESA_B2C_INITIATOR_NAME'),
        'security_credential' => env('MPESA_B2C_SECURITY_CREDENTIAL'),
        'shortcode' => env('MPESA_B2C_SHORTCODE'),
        'queue_timeout_url' => env('MPESA_B2C_QUEUE_TIMEOUT_URL'),
        'result_url' => env('MPESA_B2C_RESULT_URL'),
        'passkey' => env('MPESA_PASSKEY'),
    ],
    
    'urls' => [
        'sandbox' => [
            'auth' => 'https://sandbox.safaricom.co.ke/oauth/v1/generate',
            'b2c' => 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest',
        ],
        'production' => [
            'auth' => 'https://api.safaricom.co.ke/oauth/v1/generate',
            'b2c' => 'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest',
        ]
    ]
];