<?php

return [
    'quran' => [
        'recitation_id' => env('QURAN_RECITATION_ID', 7),
        'api_base' => env('QURAN_API_BASE_URL', 'https://api.quran.com/api/v4'),
    ],
    
    'quran_foundation' => [
        'environment' => env('QF_ENV', 'prelive'),
        'client_id' => env('QF_CLIENT_ID', env('QURAN_API_CLIENT_ID')),
        'client_secret' => env('QF_CLIENT_SECRET', env('QURAN_API_CLIENT_SECRET')),
        'redirect_uri' => env('QF_REDIRECT_URI'),
        'user_scopes' => env('QF_USER_SCOPES', 'openid offline_access user'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
];
