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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'zoom_s2s' => [
        'account_id' => env('ZOOM_S2S_ACCOUNT_ID'),
        'client_id' => env('ZOOM_S2S_CLIENT_ID'),
        'client_secret' => env('ZOOM_S2S_CLIENT_SECRET'),
        'default_user_id' => env('ZOOM_S2S_DEFAULT_USER_ID'),
        'use_instructor_email' => env('ZOOM_S2S_USE_INSTRUCTOR_EMAIL', true),
    ],

    'zoom_oauth' => [
        'client_id' => env('ZOOM_OAUTH_CLIENT_ID'),
        'client_secret' => env('ZOOM_OAUTH_CLIENT_SECRET'),
        'redirect' => env('ZOOM_OAUTH_REDIRECT_URI'),
    ],

    'zoom_meeting_sdk' => [
        'key' => env('ZOOM_MEETING_SDK_KEY'),
        'secret' => env('ZOOM_MEETING_SDK_SECRET'),
    ],

];
