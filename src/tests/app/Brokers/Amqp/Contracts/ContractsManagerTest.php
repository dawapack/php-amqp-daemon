<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Contracts;

use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerChannelsCollection;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Tests\AppTestCase;
use function DaWaPack\Chassis\Helpers\app;

class ContractsManagerTest extends AppTestCase
{
    private ContractsManager $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new ContractsManager(
            app(BrokerConfigurationInterface::class),
            new ContractsValidator()
        );
    }

    public function testSutIsInstanceOfContractsManager()
    {
        $this->assertInstanceOf(ContractsManager::class, $this->sut);
    }

    public function testSutCanReturnTestOutboundCommandsChannelConfiguration()
    {
        $channel = $this->sut->getChannel("test/outbound/commands");
        $this->assertArrayHasKey(
            "name", $channel->channelBindings->toFunctionArguments(false)
        );
        $this->assertEquals(
            "DaWaPack.DX.TestCommands", $channel->channelBindings->toFunctionArguments(false)["name"]
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
        $this->assertEmpty($channel->messageBindings->toFunctionArguments(false)["messageType"]);
    }

    public function testSutCanReturnChannelsCollection()
    {
        $channels = $this->sut->getChannels();
        $this->assertInstanceOf(BrokerChannelsCollection::class, $channels);
        $this->assertEquals(6, $channels->count());
    }
}
