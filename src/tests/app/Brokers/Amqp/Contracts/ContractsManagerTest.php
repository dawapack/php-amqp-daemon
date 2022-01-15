<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Contracts;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Tests\AppTestCase;

class ContractsManagerTest extends AppTestCase
{
    private ContractsManager $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $brokerConfigurationFixture = require __DIR__ . "/../Fixtures/Config/broker.php";
        $this->sut = new ContractsManager(
            new BrokerConfiguration($brokerConfigurationFixture),
            new ContractsValidator()
        );
    }

    public function testSutIsInstanceOfContractsManager()
    {
        $this->assertInstanceOf(ContractsManager::class, $this->sut);
    }

    public function testCanReturnRpcOutboundCommandsChannelConfiguration()
    {
        $channel = $this->sut->getInfrastructureChannel("rpc/outbound/commands");
        $this->assertArrayHasKey(
            "name", $channel->channelBindings->toFunctionArguments(false)
        );
        $this->assertEquals(
            "DaWaPack.DX.RpcCommands", $channel->channelBindings->toFunctionArguments(false)["name"]
        );
        $this->assertArrayHasKey(
            "deliveryMode", $channel->operationBindings->toFunctionArguments(false)
        );
        $this->assertEquals(
            2, $channel->operationBindings->toFunctionArguments(false)["deliveryMode"]
        );
        $this->assertArrayHasKey(
            "messageType", $channel->messageBindings->toFunctionArguments(false)
        );
        $this->assertEquals(
            "#any", $channel->messageBindings->toFunctionArguments(false)["messageType"]
        );
    }
}
