<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp;

class GeneralSubscriber extends AbstractAMQPBroker
{
    protected string $operation = self::SUBSCRIBE_OPERATION;

    /**
     * @inheritDoc
     */
    public function __construct(string $channel)
    {
        $this->channel = $channel;
        parent::__construct();
    }
}
