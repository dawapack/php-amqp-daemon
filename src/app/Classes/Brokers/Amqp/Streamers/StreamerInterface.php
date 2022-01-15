<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

use PhpAmqpLib\Channel\AMQPChannel;

interface StreamerInterface
{
    /**
     * @param int|null $id
     *
     * @return AMQPChannel
     */
    public function getChannel(?int $id = null): AMQPChannel;

    /**
     * @return bool
     */
    public function disconnect(): bool;
}
