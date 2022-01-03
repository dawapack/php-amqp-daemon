<?php
declare(strict_types=1);

namespace DaWaPack\Chassis\Helpers;

use Closure;

if (!function_exists('value')) {

    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @param mixed $args
     *
     * @return mixed
     */
    function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}
