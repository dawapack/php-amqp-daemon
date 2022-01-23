<?php
declare(strict_types=1);

namespace DaWaPack\Chassis\Helpers;

use Closure;
use DaWaPack\Classes\Brokers\Amqp\BrokerRequest;
use DaWaPack\Classes\Brokers\Amqp\BrokerResponse;
use DaWaPack\Classes\Brokers\Amqp\Handlers\MessageHandlerInterface;
use DaWaPack\Classes\Brokers\Amqp\Streamers\PublisherStreamerInterface;
use DaWaPack\Classes\Brokers\Amqp\Streamers\SubscriberStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\SubscriberStreamerInterface;

if (!function_exists('publish')) {
    /**
     * @param BrokerRequest|BrokerResponse $data
     * @param string|null $channelName
     * @param BrokerRequest|null $context
     *
     * @return void
     */
    function publish($data, ?string $channelName = null, ?BrokerRequest $context = null): void
    {
        // set routing key from given context
        if (($context instanceof BrokerRequest) && !is_null($context->getProperty('reply_to'))) {
            $data->setRoutingKey($context->getProperty('reply_to'));
        }
        app(PublisherStreamerInterface::class)->publish($data, $channelName);
    }
}

if (!function_exists('subscribe')) {
    /**
     * @param string $channelName
     * @param string $messageBagHandler - BrokerRequest::class or BrokerResponse::class
     * @param Closure|MessageHandlerInterface|null $messageHandler
     *
     * @return SubscriberStreamer
     */
    function subscribe(string $channelName, string $messageBagHandler, $messageHandler = null): SubscriberStreamer
    {
        /** @var SubscriberStreamer $subscriber */
        $subscriber = app(SubscriberStreamerInterface::class);
        return $subscriber->setChannelName($channelName)
            ->setHandler($messageBagHandler)
            ->consume($messageHandler);
    }
}