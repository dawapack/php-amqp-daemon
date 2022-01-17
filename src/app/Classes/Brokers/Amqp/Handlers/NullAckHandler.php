<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Handlers;

use PhpAmqpLib\Message\AMQPMessage;

class NullAckHandler implements AckNackHandlerInterface
{
    public function handle(AMQPMessage $message): void
    {
    }
}
