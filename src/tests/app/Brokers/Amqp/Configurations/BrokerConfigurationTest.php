<?php

declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Tests\AppTestCase;

class BrokerConfigurationTest extends AppTestCase
{
    private BrokerConfiguration $sut;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $brokerConfigurationFixture = require __DIR__ . "/../../../../app/Brokers/Amqp/Fixtures/Config/broker.php";
        $this->sut = new BrokerConfiguration($brokerConfigurationFixture);
    }

    /**
     * @return void
     */
    public function testSutCanReturnSelectedConnectionName(): void
    {
        $this->assertIsString($this->sut->getConnection());
    }

    /**
     * @return void
     */
    public function testSutCanReturnSelectedConnectionProperties(): void
    {
        $this->assertIsObject($this->sut->getConnectionConfiguration());
        $this->assertObjectHasAttribute("protocol", $this->sut->getConnectionConfiguration());
    }

    /**
     * @return void
     */
    public function testSutCanReturnSelectedContractName(): void
    {
        $this->assertIsString($this->sut->getContract());
    }

    /**
     * @return void
     */
    public function testSutCanReturnSelectedContractProperties(): void
    {
        $this->assertIsObject($this->sut->getContractConfiguration());
        $this->assertObjectHasAttribute("driver", $this->sut->getContractConfiguration());
    }

    /**
     * @return void
     */
    public function testSutCanReturnAmqpConnectionFunctionArguments(): void
    {
        $functionArguments = $this->sut->getConnectionConfiguration()->toFunctionArguments(false);
        $this->assertIsArray($functionArguments);
        $this->assertArrayHasKey("host", $functionArguments);

        $functionArguments = $this->sut->getConnectionConfiguration()->toFunctionArguments(true);
        $this->assertIsArray($functionArguments);
        $this->assertNotEmpty($functionArguments);
    }

    /**
     * @return void
     */
    public function testSutCanReturnAmqpLazyConnectionFunctionArguments(): void
    {
        $functionArguments = $this->sut
            ->getConnectionConfiguration()
            ->toLazyConnectionFunctionArguments(false);
        $this->assertIsArray($functionArguments);
        $this->assertArrayHasKey("host", $functionArguments);
        $this->assertArrayHasKey("insist", $functionArguments["options"]);
    }
}
