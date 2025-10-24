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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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
    'openai' => [
        'key'        => env('OPENAI_API_KEY'),
        'model'      => env('OPENAI_MODEL', 'gpt-5-nano'),
        'mock'       => env('OPENAI_MOCK', false),
        'timeout'    => (int) env('OPENAI_TIMEOUT', 20),
        'retries'    => (int) env('OPENAI_RETRIES', 2),
        'retry_sleep' => (int) env('OPENAI_RETRY_SLEEP', 800),
    ],
    'open_meteo' => [
        'base' => env('OPEN_METEO_BASE', 'https://api.open-meteo.com/v1'),
    ],

];
