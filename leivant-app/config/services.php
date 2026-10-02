<?php

return [
    'azam_pay' => [
        'app_name' => env('AZAMPAY_APP_NAME'),
        'client_id' => env('AZAMPAY_CLIENT_ID'),
        'client_secret' => env('AZAMPAY_CLIENT_SECRET'),
        'base_url' => env('AZAMPAY_BASE_URL', 'https://sandbox.azampay.co.tz'),
        'mock' => (bool) env('AZAMPAY_MOCK', true),
    ],
    'whatsapp' => [
        'token' => env('WHATSAPP_API_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'owner_number' => env('WHATSAPP_OWNER_NUMBER', '255717970799'),
    ],
];
