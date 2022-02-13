<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Brokers\Amqp\Handlers;

use PhpAmqpLib\Message\AMQPMessage;

interface AckNackHandlerInterface
{
    /**
     * @param AMQPMessage $message
     *
     * @return void
     */
    public function handle(AMQPMessage $message): void;
}