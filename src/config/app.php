<?php

namespace DaWaPack\Config;

use function Chassis\Helpers\env;

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
    | use DEBUG level for development
    | use WARNING level for production
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
