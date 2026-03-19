<?php

return [
    'quran' => [
        'recitation_id' => env('QURAN_RECITATION_ID', 7),
        'api_base' => env('QURAN_API_BASE_URL', 'https://api.quran.com/api/v4'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
];
