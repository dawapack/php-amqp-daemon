<?php

namespace DaWaPack\Chassis\Helpers;

use DaWaPack\Chassis\Support\Env;

if (! function_exists('env')) {

    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        return Env::get($key, $default);
    }
}
