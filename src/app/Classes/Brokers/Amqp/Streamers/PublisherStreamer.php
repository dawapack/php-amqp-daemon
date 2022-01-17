<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

use DaWaPack\Classes\Brokers\Amqp\Handlers\AckNackHandlerInterface;
use DaWaPack\Classes\Brokers\Amqp\Handlers\NullAckHandler;
use DaWaPack\Classes\Brokers\Amqp\Handlers\NullNackHandler;
use DaWaPack\Classes\Messages\Request;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class PublisherStreamer extends AbstractStreamer implements PublisherStreamerInterface
{
    private AckNackHandlerInterface $ackHandler;
    private AckNackHandlerInterface $nackHandler;
    private AMQPChannel $streamerChannel;

    /**
     * @inheritDoc
     */
    public function getAckHandler(): AckNackHandlerInterface
    {
        return $this->ackHandler;
    }

    /**
     * @inheritDoc
     */
    public function setAckHandler(AckNackHandlerInterface $ackHandler): PublisherStreamerInterface
    {
        $this->ackHandler = $ackHandler;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNackHandler(): AckNackHandlerInterface
    {
        return $this->nackHandler;
    }

    /**
     * @inheritDoc
     */
    public function setNackHandler(AckNackHandlerInterface $nackHandler): PublisherStreamerInterface
    {
        $this->nackHandler = $nackHandler;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function publish(Request $request, $publishAcknowledgeTimeout = 5): void
    {
        try {
            // get a new channel
            $this->streamerChannel = $this->getChannel();
            // use ack & nack mechanism
            $this->enablePublishConfirmMode();
            // basic publish
            $this->streamerChannel->basic_publish(
                $request->toAmqpMessage(),

            );
            // wait for acknowledgements - ack or nack
            $this->streamerChannel->wait_for_pending_acks($publishAcknowledgeTimeout);
        } catch (Throwable $reason) {

        }
        // close the channel
        if (isset($this->streamerChannel) && $this->streamerChannel->is_open()) {
            $this->streamerChannel->close();
        }
        unset($this->streamerChannel);
    }

    private function enablePublishConfirmMode(): void
    {
        !isset($this->ackHandler) && $this->setAckHandler(new NullAckHandler());
        !isset($this->nackHandler) && $this->setNackHandler(new NullNackHandler());

        $ackHandler = $this->getAckHandler();
        $nackHandler = $this->getNackHandler();

        $this->streamerChannel->set_ack_handler(function (AMQPMessage $message) use ($ackHandler) {
            $ackHandler->handle($message);
        });
        $this->streamerChannel->set_nack_handler(function (AMQPMessage $message) use ($nackHandler) {
            $nackHandler->handle($message);
        });

        $this->streamerChannel->confirm_select();
    }
}
