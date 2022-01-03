<?php
declare(strict_types=1);

namespace DaWaPack\Config\Schemas;

use Nette\Schema\Expect;
use Nette\Schema\Schema;

class App
{

    public static function getSchema(): Schema
    {
        return Expect::structure([
            'name' => Expect::string()->assert(function (string $name) {
                $length = strlen($name);
                return $length >= 5 && $length <= 64 ? true : false;
            })->required(),
            'env' => Expect::anyOf('production', 'staging', 'development', 'testing')->required(),
            'loglevel' => Expect::anyOf(
                'DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'
            )->required(),
            'timezone' => Expect::string()->assert(function (string $name) {
                $length = strlen($name);
                return $length >= 3 && $length <= 255 ? true : false;
            })->required(),
            'locale' => Expect::string()->assert(function (string $name) {
                return strlen($name) == 2;
            })->required(),
            'key' => Expect::string()->required(),
            'cipher' => Expect::string()->required()
        ]);
    }
}
