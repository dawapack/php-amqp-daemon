<?php

declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Contracts;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Classes\Brokers\Amqp\Contracts\Exceptions\ContractsValidatorException;
use DaWaPack\Tests\AppTestCase;

class ContractsValidatorTest extends AppTestCase
{
    /**
     * @return void
     * @throws ContractsValidatorException
     */
    public function testValidatorMustFailWithContractsValidatorException(): void
    {
        $this->expectException(ContractsValidatorException::class);
        $broker = require __DIR__ . "/../Fixtures/Config/broker.php";
        $broker["contracts"]["asyncapi"]["definitions"]["infrastructure"] = "wrong-tests-infrastructure.yml";
        new ContractsManager(
            new BrokerConfiguration($broker),
            new ContractsValidator()
        );
    }
}