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
    'nikah' => [
        'verification_fee' => env('NIKAH_VERIFICATION_FEE', 500), // PKR
    ],

    'webpush' => [
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'subject' => env('VAPID_SUBJECT'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        // The Flutter app authenticates with its own OAuth client(s) —
        // separate, SHA-1-fingerprinted Android/iOS client IDs from the
        // website's — registered in the Google Cloud Console specifically
        // for the mobile app. Api\V1\AuthController checks the ID token's
        // `aud` claim against every id listed here (comma-separated), so
        // both Android and iOS client IDs can be accepted at once. Empty
        // until the user provides these; social login on mobile will fail
        // validation (not silently succeed) until this is set.
        'mobile_client_ids' => array_filter(explode(',', env('GOOGLE_MOBILE_CLIENT_IDS', ''))),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    // Social-posting integrations (Admin > Integrations) — separate from
    // the 'google'/'facebook' blocks above, which are for user login.
    // Facebook posting reuses the same Meta App ID/secret (one App can
    // register multiple redirect URIs and request different scopes per
    // flow) but Twitter/YouTube/TikTok have no login use in this app, so
    // they get their own dedicated client credentials.
    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
    ],

    'youtube' => [
        'client_id' => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
    ],

    'tiktok' => [
        'client_id' => env('TIKTOK_CLIENT_ID'),
        'client_secret' => env('TIKTOK_CLIENT_SECRET'),
    ],

    // Threads issues its own dedicated App ID/Secret, separate from the
    // main Meta App ID used for Facebook Login above — using the Facebook
    // App ID here fails outright, this genuinely has to be a different pair.
    'threads' => [
        'client_id' => env('THREADS_CLIENT_ID'),
        'client_secret' => env('THREADS_CLIENT_SECRET'),
    ],

    // Sends push notifications to the Nikah Counselor app via FCM — the
    // service-account key itself lives outside git (see .gitignore) at
    // storage/app/firebase/firebase-adminsdk.json.
    'firebase' => [
        'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/firebase-adminsdk.json')),
    ],

];
