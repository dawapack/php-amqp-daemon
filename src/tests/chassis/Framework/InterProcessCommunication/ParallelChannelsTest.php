<?php

declare(strict_types=1);

namespace DaWaPack\Tests\chassis\Framework\InterProcessCommunication;

use DaWaPack\Chassis\Framework\InterProcessCommunication\DataTransferObject\IPCMessage;
use DaWaPack\Chassis\Framework\InterProcessCommunication\ParallelChannels;
use DaWaPack\Tests\AppTestCase;
use Exception;
use parallel\Channel;
use parallel\Events;
use parallel\Events\Event;

class ParallelChannelsTest extends AppTestCase
{
    private ParallelChannels $sut;
    private Event $event;
    private Events $events;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new ParallelChannels();
    }

    /**
     * @return void
     */
    public function testSutCanSetWorkerChannel(): void
    {
        $this->sut->setWorkerChannel(
            (Channel::make("inboundCreate", Channel::Infinite))::open("inboundCreate")
        );
        $this->assertInstanceOf(Channel::class, $this->sut->getWorkerChannel());
    }

    /**
     * @return void
     */
    public function testSutCanSetThreadChannel(): void
    {
        $this->sut->setThreadChannel(
            (Channel::make("outboundCreate", Channel::Infinite))::open("outboundCreate")
        );
        $this->assertInstanceOf(Channel::class, $this->sut->getThreadChannel());
    }

    /**
     * @return void
     */
    public function testSutCanDestroyChannels(): void
    {
        $this->sut->setWorkerChannel(
            (Channel::make("inboundDestroy", Channel::Infinite))::open("inboundDestroy")
        );
        $this->sut->setThreadChannel(
            (Channel::make("outboundDestroy", Channel::Infinite))::open("outboundDestroy")
        );
        $this->sut->destroy();
        $this->assertNull($this->sut->getWorkerChannel());
        $this->assertNull($this->sut->getThreadChannel());
    }

    /**
     * @return void
     */
    public function testSutCanSendMessageThroughWorkerChannels(): void
    {
        $channelName = 'workerchannel';
        $test = (object)[
            'channel' => (Channel::make($channelName, Channel::Infinite))::open($channelName),
            'message' => (new IPCMessage())->set('doSomething', 'worker channel')
        ];
        // set channel
        $this->sut->setWorkerChannel($test->channel);
        // send message to
        $this->sut->sendTo($this->sut->getWorkerChannel(), $test->message);
        // receive message from
        $message = $this->receiveFrom($this->sut->getWorkerChannel());

        $this->assertEquals($test->message, $message);
    }

    /**
     * @return void
     */
    public function testSutCanSendMessageThroughThreadChannels(): void
    {
        $channelName = 'threadchannel';
        $test = (object)[
            'channel' => (Channel::make($channelName, Channel::Infinite))::open($channelName),
            'message' => (new IPCMessage())->set('doSomething', 'thread channel')
        ];
        // set channel
        $this->sut->setThreadChannel($test->channel);
        // send message to
        $this->sut->sendTo($this->sut->getThreadChannel(), $test->message);
        // receive message from
        $message = $this->receiveFrom($this->sut->getThreadChannel());

        $this->assertEquals($test->message, $message);
    }

    private function receiveFrom(Channel $channel): IPCMessage
    {
        $events = new Events();
        $events->setBlocking(true);
        $events->setTimeout(5);
        $events->addChannel($channel);
        $event = $events->poll();
        if (is_null($event)) {
            throw new Exception("event pool timeout");
        }
        return new IPCMessage($event->value);
    }
}
