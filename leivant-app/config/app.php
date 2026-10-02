<?php

return [
    'name' => env('APP_NAME', 'Leivant Construction Solutions'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'https://leivantconstruction.com'),
    'timezone' => env('APP_TIMEZONE', 'Africa/Dar_es_Salaam'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(explode(',', env('APP_PREVIOUS_KEYS', ''))),
    ],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
    'company' => [
        'name' => 'Leivant Construction Solutions Company',
        'brela_no' => env('VANT_BRELA_NO', '626936'),
        'owner' => env('VANT_OWNER', 'Gaspary Jovin Lyamuya'),
        'location' => env('VANT_LOCATION', 'Majumba site, Kipawa Airport, Dar es Salaam, Tanzania'),
        'map_url' => env('VANT_MAP_URL', 'https://www.google.com/maps?q=-6.8664952,39.1899987&z=17&hl=en'),
        'latitude' => env('VANT_LATITUDE', '-6.8664952'),
        'longitude' => env('VANT_LONGITUDE', '39.1899987'),
        'phone' => env('VANT_PHONE', '0717 970 799'),
        'phone_tel' => env('VANT_PHONE_TEL', '+255717970799'),
        'email' => env('VANT_EMAIL', 'info@leivantconstruction.com'),
        'whatsapp' => env('VANT_WHATSAPP_URL', 'https://wa.me/255717970799'),
    ],
    'analytics' => [
        'google_id' => env('VANT_GA_ID'),
        'clarity_id' => env('VANT_CLARITY_ID'),
        'facebook_pixel_id' => env('VANT_FACEBOOK_PIXEL_ID'),
    ],
    'social' => [
        'facebook' => env('VANT_FACEBOOK_URL'),
        'x' => env('VANT_X_URL'),
        'instagram' => env('VANT_INSTAGRAM_URL'),
        'youtube' => env('VANT_YOUTUBE_URL'),
        'linkedin' => env('VANT_LINKEDIN_URL'),
    ],
];
