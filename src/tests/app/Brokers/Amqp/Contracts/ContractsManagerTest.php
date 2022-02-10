<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Contracts;

use DaWaPack\Classes\Brokers\Amqp\BrokerRequest;
use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\BrokerChannelsCollection;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Classes\Brokers\Amqp\Contracts\Exceptions\ContractsValidatorException;
use DaWaPack\Classes\Brokers\Exceptions\StreamerChannelNameNotFoundException;
use DaWaPack\Tests\AppTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

use function DaWaPack\Chassis\Helpers\app;

class ContractsManagerTest extends AppTestCase
{
    private ContractsManager $sut;

    /**
     * @return void
     * @throws ContractsValidatorException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new ContractsManager(
            app(BrokerConfigurationInterface::class),
            new ContractsValidator()
        );
    }

    /**
     * @return void
     */
    public function testSutIsInstanceOfContractsManager(): void
    {
        $this->assertInstanceOf(ContractsManager::class, $this->sut);
    }

    /**
     * @return void
     */
    public function testSutCanReturnTestOutboundCommandsChannelConfiguration(): void
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

    /**
     * @return void
     */
    public function testSutCanReturnChannelsCollection(): void
    {
        $channels = $this->sut->getChannels();
        $this->assertInstanceOf(BrokerChannelsCollection::class, $channels);
        $this->assertTrue($channels->count() > 0);
    }

    /**
     * @return void
     */
    public function testSutCanReturnValidLazyConnectionFunctionArguments(): void
    {
        $arguments = $this->sut->toLazyConnectionFunctionArguments();
        $this->assertIsArray($arguments);
        $this->assertArrayHasKey("options", $arguments);
        $this->assertArrayHasKey("login_method", $arguments["options"]);
    }

    /**
     * @return void
     * @throws StreamerChannelNameNotFoundException
     */
    public function testSutMustThrowAnExceptionCallingToBasicPublishArgumentsWithWrongChannelName(): void
    {
        $this->expectException(StreamerChannelNameNotFoundException::class);
        $this->sut->toBasicPublishFunctionArguments(
            'wrong/channel/name',
            new BrokerRequest('')
        );
    }

    /**
     * @return void
     * @throws StreamerChannelNameNotFoundException
     */
    public function testSutMustThrowAnExceptionCallingToBasicConsumeArgumentsWithWrongChannelName(): void
    {
        $this->expectException(StreamerChannelNameNotFoundException::class);
        $this->sut->toBasicConsumeFunctionArguments(
            'wrong/channel/name',
            function(){}
        );
    }

    /**
     * @return void
     * @throws ContractsValidatorException
     * @throws Throwable
     */
    public function testSutMustThrowAnExceptionLoadingConfigurationWithEmptyPathValidator(): void
    {
        $this->expectException(ContractsValidatorException::class);
        $broker = $this->app->config("broker");
        $broker["contracts"]["asyncapi"]["paths"]["validator"] = '';
        new ContractsManager(
            new BrokerConfiguration($broker),
            new ContractsValidator()
        );
    }
}
