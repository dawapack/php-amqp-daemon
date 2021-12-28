<?php

namespace DaWaPack\Config;

use function DaWaPack\Chassis\Helpers\env;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    */

    'name' => env('APP_NAME', 'PHP AMQP Daemon'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Log Level
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'loglevel' => env('APP_LOGLEVEL', 'DEBUG'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    */

    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key and Cipher
    |--------------------------------------------------------------------------
    */
    'key' => env('APP_KEY', ''),
    'cipher' => 'AES-256-CBC',
];