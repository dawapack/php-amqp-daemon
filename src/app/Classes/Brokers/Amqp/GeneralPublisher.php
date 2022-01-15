<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp;

class GeneralPublisher extends AbstractAMQPBroker
{
    protected string $operation = self::PUBLISH_OPERATION;

    /**
     * @inheritDoc
     */
    public function __construct(string $channel)
    {
        $this->channel = $channel;
        parent::__construct();
    }
}
