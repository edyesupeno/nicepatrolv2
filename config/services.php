<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'maps' => [
        'tile_server' => env('MAPS_TILE_SERVER', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
        'attribution' => env('MAPS_ATTRIBUTION', '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'),
        'max_zoom' => env('MAPS_MAX_ZOOM', 18),
        'min_zoom' => env('MAPS_MIN_ZOOM', 1),
        'default_center_lat' => env('MAPS_DEFAULT_CENTER_LAT', -6.2088),
        'default_center_lng' => env('MAPS_DEFAULT_CENTER_LNG', 106.8456),
        'default_zoom' => env('MAPS_DEFAULT_ZOOM', 15),
        
        // Adonara Maps for reverse geocoding
        'reverse_geocoding_url' => env('MAPS_REVERSE_GEOCODING_URL', 'https://maps.adonara.co.id/reverse'),
    ],

    'whatsapp' => [
        'base_url' => env('WHATSAPP_API_BASE_URL', 'https://api.starsender.online/api/send'),
        'token' => env('WHATSAPP_API_TOKEN'),
    ],

];
