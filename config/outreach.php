<?php

return [
    'defaults' => [
        'language' => env('OUTREACH_DEFAULT_LANGUAGE', 'tr'),
        'timezone' => env('OUTREACH_DEFAULT_TIMEZONE', env('APP_TIMEZONE', 'UTC')),
        'daily_send_limit' => (int) env('OUTREACH_DAILY_LIMIT', 40),
        'hourly_send_limit' => (int) env('OUTREACH_HOURLY_LIMIT', 10),
        'min_delay_seconds' => (int) env('OUTREACH_MIN_DELAY_SECONDS', 180),
        'send_start_hour' => (int) env('OUTREACH_SEND_START_HOUR', 9),
        'send_end_hour' => (int) env('OUTREACH_SEND_END_HOUR', 18),
        'require_approval' => (bool) env('OUTREACH_REQUIRE_APPROVAL', true),
    ],
    'openai' => [
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_OUTREACH_MODEL', 'gpt-5-mini'),
        'timeout' => (int) env('OPENAI_OUTREACH_TIMEOUT', 60),
    ],
    'lusha' => [
        'base_url' => env('LUSHA_BASE_URL', 'https://api.lusha.com'),
        'api_key' => env('LUSHA_API_KEY'),
        'api_key_prefix' => env('LUSHA_API_KEY_PREFIX', ''),
        'send_authorization_header' => (bool) env('LUSHA_SEND_AUTHORIZATION_HEADER', false),
        'search_path' => env('LUSHA_SEARCH_PATH', '/prospecting/contact/search'),
        'enrich_path' => env('LUSHA_ENRICH_PATH', '/prospecting/contact/enrich'),
        'timeout' => (int) env('LUSHA_TIMEOUT', 45),
    ],
    'imap' => [
        'host' => env('OUTREACH_IMAP_HOST'),
        'port' => (int) env('OUTREACH_IMAP_PORT', 993),
        'encryption' => env('OUTREACH_IMAP_ENCRYPTION', 'ssl'),
        'username' => env('OUTREACH_IMAP_USERNAME'),
        'password' => env('OUTREACH_IMAP_PASSWORD'),
        'mailbox' => env('OUTREACH_IMAP_MAILBOX', 'INBOX'),
        'search' => env('OUTREACH_IMAP_SEARCH', 'UNSEEN'),
    ],
];
