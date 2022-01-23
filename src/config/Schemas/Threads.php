<?php
declare(strict_types=1);

namespace DaWaPack\Config\Schemas;

use Nette\Schema\Expect;
use Nette\Schema\Schema;

class Threads
{

    public static function getSchema(): Schema
    {
        return Expect::structure([
            'handler' => Expect::string()->required(),
            'minimum' => Expect::int()->required(),
            'maximum' => Expect::int()->required(),
            'triggers' => Expect::array()->required(),
            'ttl' => Expect::int()->required(),
            'max_jobs' => Expect::int()->required(),
            'infrastructure' => self::infrastructureSchema(),
            'configuration' => self::configurationSchema(),
            'worker' => self::workerSchema()
        ]);
    }

    private static function infrastructureSchema(): Schema
    {
        return Expect::structure([
            'minimum' => Expect::int()->required(),
            'maximum' => Expect::int()->required(),
            'enabled' => Expect::bool()->required(),
        ]);
    }

    private static function configurationSchema(): Schema
    {
        return Expect::structure([
            'minimum' => Expect::int()->required(),
            'maximum' => Expect::int()->required(),
            'enabled' => Expect::bool()->required(),
        ]);
    }

    private static function workerSchema(): Schema
    {
        return Expect::structure([
            'channels' => Expect::arrayOf(self::workerChannelSchema()),
            'enabled' => Expect::bool()->required(),
        ]);
    }

    private static function workerChannelSchema(): Schema
    {
        return Expect::structure([
            'handler' => Expect::string(),
            'minimum' => Expect::int(),
            'maximum' => Expect::int(),
            'triggers' => Expect::array(),
            'ttl' => Expect::int(),
            'max_jobs' => Expect::int(),
            'channelName' => Expect::string(),
            'enabled' => Expect::bool(),
        ])->skipDefaults(true);
    }
}
