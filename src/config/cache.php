<?php

namespace DaWaPack\Config;

use function Chassis\Helpers\env;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | Supported: "redis"
    |
    */

    'default' => env('CACHE_STORE', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    */

    'stores' => [
        // see https://github.com/predis/predis#client-configuration
        'redis' => [
            'driver' => 'redis',
            'servers' => env('REDIS_SERVERS', 'tcp://localhost:6379'),
            'connection' => [
                'timeout' => 5.0,
                'retryInterval' => 5,
                'readTimeout' => 1,0,
            ],
            'database' => (int)env('REDIS_DATABASE', 0),
            'options' => [
                'prefix' => env('REDIS_OPTIONS_PREFIX', 'cache')
            ]
        ],
        'memcached' => [],
    ],
];
