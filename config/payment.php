<?php

return [
    'paystack' => [
        'endpoint' => env('PAYSTACK_ENDPOINT'),
        'secret-key' => env('PAYSTACK_SECRET_KEY'),
    ],
    'squad' => [
        'endpoint' => env('SQUAD_ENDPOINT'),
        'secret-key' => env('SQUAD_PRIVATE_KEY'),
        'public-key' => env('SQUAD_PUBLIC_KEY'),
    ],
    'moniepoint' => [
        'endpoint' => env('MONIEPOINT_ENDPOINT'),
        'wallet-acc-num' => env('MONIEPOINT_WALLET_ACC_NUM'),
        'api-key' => env('MONIEPOINT_API_KEY'),
        'contract-code' => env('MONIEPOINT_CONTRACT_CODE'),
        'secret-key' => env('MONIEPOINT_SECRET_KEY'),
    ],
    'remitta' => [
        'endpoint' => env('REMITTA_ENDPOINT'),
        'api-key' => env('REMITTA_API_KEY'),
        'service-type-id' => env('REMITTA_SERVICE_TYPE_ID'),
        'merchant-id' => env('REMITTA_MERCHANT_ID'),
        'public-key' => env('REMITTA_PUBLIC_KEY'),
        'secret-key' => env('REMITTA_SECRET_KEY'),
    ],
];
