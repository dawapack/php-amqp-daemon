<?php

declare(strict_types=1);

namespace DaWaPack\Config\Schemas;

use Nette\Schema\Expect;
use Nette\Schema\Schema;

class Cache
{
    public static function getSchema(): Schema
    {
        return Expect::structure([
            'default' => Expect::string()->required(),
            'stores' => self::storesSchema(),
        ]);
    }

    private static function storesSchema(): Schema
    {
        return Expect::structure([
            'redis' => self::redisSchema(),
            'memcached' => Expect::array(),
        ]);
    }

    private static function redisSchema(): Schema
    {
        return Expect::structure([
            'driver' => Expect::string()->required(),
            'servers' => Expect::string()->required(),
            'connection' => Expect::array(),
            'database' => Expect::int()->required(),
            'options' => Expect::array()
        ])->skipDefaults(true);
    }
}
