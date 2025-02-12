<?php

return [
    'api_url' => env('PAYWAY_API_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase'),
    'api_key' => env('PAYWAY_API_KEY', '23624d0197a8bcf9de237c11439e9d8bd946b828'),
    'merchant_id' => env('PAYWAY_MERCHANT_ID', 'ec438950'),
];
