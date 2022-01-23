<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Messages;

use DaWaPack\Chassis\Application;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamerInterface;
use function DaWaPack\Chassis\Helpers\app;
use function DaWaPack\Chassis\Helpers\publish;

class Request extends AbstractRequestResponseMessage implements RequestResponseInterface
{
    /**
     * Send this request through a given channel
     *
     * @param ?string $channelName
     *
     * @return void
     */
    public function send(?string $channelName = null): void
    {
        // do not publish a request built by a consumer
        if (isset($this->consumerTag)) {
            return;
        }
        $channelName = $channelName ?? '';
        publish($this, $channelName);
    }
}
