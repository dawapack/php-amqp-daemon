<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Handlers;

use PhpAmqpLib\Message\AMQPMessage;

class MessageHandler implements MessageHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(AMQPMessage $message): void
    {
        // just acknowledge the message
        $message->ack();
    }
}
