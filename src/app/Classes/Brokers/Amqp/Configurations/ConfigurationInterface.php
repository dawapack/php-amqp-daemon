<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Configurations;

interface ConfigurationInterface
{

    /**
     * @return array
     */
    public function toFunctionArguments(bool $onlyValues = true): array;
}
