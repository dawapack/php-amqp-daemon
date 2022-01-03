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
    // How many times the thread will live - default: 15min
    'ttl' => 900,
    // Haw many jobs before restart the thread - default: 150
    'max_jobs' => 150,

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
                'minimum' => 5,
                'maximum' => 50,
                'max_jobs' => 200,
                'enabled' => true,
            ],
            'inbound/responses' => [
                'minimum' => 5,
                'maximum' => 30,
                'max_jobs' => 100,
                'enabled' => true,
            ],
            'inbound/events' => [
                'minimum' => 10,
                'maximum' => 50,
                'max_jobs' => 100,
                'enabled' => true,
            ],
        ],
        'enabled' => true
    ],
];
