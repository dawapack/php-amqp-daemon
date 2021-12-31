<?php
declare(strict_types=1);

namespace DaWaPack\Config\Schemas;

use Nette\Schema\Expect;
use Nette\Schema\Schema;

class Broker
{

    public static function getSchema(): Schema
    {
        return Expect::structure([
            'connection' => Expect::string()->required(),
            'contract' => Expect::string()->required(),
            'connections' => Expect::array()->required(),
            'contracts' => Expect::array()->required(),
        ]);
    }
}
