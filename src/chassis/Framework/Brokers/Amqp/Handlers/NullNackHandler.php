<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Brokers\Amqp\Handlers;

use PhpAmqpLib\Message\AMQPMessage;

class NullNackHandler implements AckNackHandlerInterface
{
    public function handle(AMQPMessage $message): void
    {
    }
}
