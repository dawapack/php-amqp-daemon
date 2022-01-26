<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Configurations;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Tests\AppTestCase;

class BrokerConfigurationTest extends AppTestCase
{
    private BrokerConfiguration $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $brokerConfigurationFixture = require __DIR__ . "/../../../../app/Brokers/Amqp/Fixtures/Config/broker.php";
        $this->sut = new BrokerConfiguration($brokerConfigurationFixture);
    }

    public function testSutCanReturnSelectedConnectionName()
    {
        $this->assertIsString($this->sut->getConnection());
    }

    public function testSutCanReturnSelectedConnectionProperties()
    {
        $this->assertIsObject($this->sut->getConnectionConfiguration());
        $this->assertObjectHasAttribute("protocol", $this->sut->getConnectionConfiguration());
    }

    public function testSutCanReturnSelectedContractName()
    {
        $this->assertIsString($this->sut->getContract());
    }

    public function testSutCanReturnSelectedContractProperties()
    {
        $this->assertIsObject($this->sut->getContractConfiguration());
        $this->assertObjectHasAttribute("driver", $this->sut->getContractConfiguration());
    }

    public function testSutCanReturnAmqpConnectionFunctionArguments()
    {
        $functionArguments = $this->sut->getConnectionConfiguration()->toFunctionArguments(false);
        $this->assertIsArray($functionArguments);
        $this->assertArrayHasKey("host", $functionArguments);
    }

    public function testSutCanReturnAmqpLazyConnectionFunctionArguments()
    {
        $functionArguments = $this->sut
            ->getConnectionConfiguration()
            ->toLazyConnectionFunctionArguments(false);
        $this->assertIsArray($functionArguments);
        $this->assertArrayHasKey("host", $functionArguments);
        $this->assertArrayHasKey("insist", $functionArguments["options"]);
    }
}
