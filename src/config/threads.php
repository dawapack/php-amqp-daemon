<?php

namespace DaWaPack\Config;

return [

    /*
    |--------------------------------------------------------------------------
    | Default settings
    |--------------------------------------------------------------------------
    */
    // minimum vertical scaling
    'minimum' => 2,
    // maximum vertical scaling
    'maximum' => 30,
    // triggers used by scaling mechanism
    'triggers' => [
        35 => 1,
        55 => 2,
        75 => 5,
        90 => 10,
    ],
    // How many times the thread will live - default: 1 hour
    'ttl' => 3600,
    // Haw many jobs before restart the thread - default: 1800
    'max_jobs' => 1800,

    /*
    |--------------------------------------------------------------------------
    | Infrastructure default thread settings
    |--------------------------------------------------------------------------
    */
    'infrastructure' => [
        'minimum' => 1,
        'maximum' => 1,
        'enabled' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Centralized configuration default thread settings
    |--------------------------------------------------------------------------
    */
    'configuration' => [
        'minimum' => 1,
        'maximum' => 1,
        'enabled' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Worker thread(s) default settings
    |--------------------------------------------------------------------------
    */
    'worker' => [
        'channels' => [
            'inbound/commands' => [
                'minimum' => 15,
                'maximum' => 50,
                'enabled' => true,
            ],
            'inbound/responses' => [
                'minimum' => 5,
                'maximum' => 50,
                'enabled' => true,
            ],
            'inbound/events' => [
                'minimum' => 5,
                'maximum' => 50,
                'enabled' => true,
            ],
        ],
        'enabled' => true
    ],
];
