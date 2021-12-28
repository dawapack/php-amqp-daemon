<?php

namespace DaWaPack\Config;

return [

    /*
    |--------------------------------------------------------------------------
    | Default settings
    |--------------------------------------------------------------------------
    */
    // vertical scaling - minimum threads
    'minimum' => 2,
    // vertical scaling - maximum threads
    'maximum' => 30,
    // scaling triggers
    'triggers' => [
        50 => 2,
        70 => 5,
        90 => 10,
    ],
    // time to live - default: 15min
    'ttl' => 900,
    // max job before restart the worker - default: 150
    'max_jobs' => 150,

    /*
    |--------------------------------------------------------------------------
    | Infrastructure thread(s) settings
    |--------------------------------------------------------------------------
    */
    'infrastructure' => [
        'minimum' => 1,
        'maximum' => 1,
        'enabled' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Centralized configuration thread(s) settings
    |--------------------------------------------------------------------------
    */
    'configuration' => [
        'minimum' => 1,
        'maximum' => 1,
        'enabled' => false
    ],
];