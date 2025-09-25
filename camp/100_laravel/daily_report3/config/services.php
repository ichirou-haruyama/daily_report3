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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Google Sheets 読み取り設定
    'google_sheets' => [
        'project_id' => env('GOOGLE_SHEETS_PROJECT_ID'),
        'service_account_json_path' => env('GOOGLE_SHEETS_SERVICE_ACCOUNT_JSON_PATH', storage_path('app/keys/google_sa.json')),
        // リンク（スプレッドシートID）と読み取りレンジは .env で差し替え可能
        'spreadsheet_id' => env('GOOGLE_SHEETS_SPREADSHEET_ID'),
        'range' => env('GOOGLE_SHEETS_RANGE', '工事台帳!A:Z'),
        // 読み取りに必要なスコープ
        'scopes' => explode(' ', env('GOOGLE_SHEETS_DRIVE_SCOPES', 'https://www.googleapis.com/auth/drive.readonly https://www.googleapis.com/auth/spreadsheets.readonly')),
    ],

];
