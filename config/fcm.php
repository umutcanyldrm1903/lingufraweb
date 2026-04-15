<?php

return [
    'enabled' => (bool) env('FCM_ENABLED', false),
    'project_id' => env('FCM_PROJECT_ID'),
    'service_account_email' => env('FCM_SERVICE_ACCOUNT_EMAIL'),
    'service_account_private_key' => env('FCM_SERVICE_ACCOUNT_PRIVATE_KEY'),
    'oauth_scope' => 'https://www.googleapis.com/auth/firebase.messaging',
    'token_cache_key' => 'fcm_access_token',
    'token_cache_ttl_minutes' => 55,
    'lesson_reminder_minutes' => (int) env('FCM_LESSON_REMINDER_MINUTES', 30),
];
