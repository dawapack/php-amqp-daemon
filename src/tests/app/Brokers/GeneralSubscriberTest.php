<?php
declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers;

use DaWaPack\Classes\Brokers\Amqp\BrokerInterface;
use DaWaPack\Classes\Brokers\Amqp\GeneralSubscriber;
use DaWaPack\Tests\AppTestCase;

class GeneralSubscriberTest extends AppTestCase
{

    private BrokerInterface $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new GeneralSubscriber('inbound/requests');
    }

    public function testGeneralSubscriberInstantiation()
    {
        $this->assertInstanceOf(BrokerInterface::class, $this->sut);
    }
}
