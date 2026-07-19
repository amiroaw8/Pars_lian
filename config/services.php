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

    'kavenegar' => [
        'api_key' => env('KAVENEGAR_API_KEY', ''),
        'sender' => env('KAVENEGAR_SENDER', '1000'),
        'enabled' => env('KAVENEGAR_ENABLED', ! empty(env('KAVENEGAR_API_KEY'))),
    ],

    'smsir' => [
        'api_key' => env('SMSIR_API_KEY', ''),
        'line_number' => env('SMSIR_LINE_NUMBER', ''),
        'enabled' => env('SMSIR_ENABLED', true),
        'base_url' => 'https://api.sms.ir/',
    ],

    'zarinpal' => [
        'merchant_id' => env('ZARINPAL_MERCHANT_ID', ''),
    ],

];
