<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\Heartbeat\PCNTLHeartbeatSender;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use Throwable;

abstract class AbstractStreamer implements StreamerInterface
{

    protected AMQPStreamConnection $streamerConnection;
    protected PCNTLHeartbeatSender $heartbeatSender;
    protected string $channelName;
    protected string $operation;

    /**
     * AbstractStreamer constructor.
     *
     * @param AMQPStreamConnection $streamerConnection
     * @param string $channelName
     * @param string $operation
     */
    public function __construct(
        AMQPStreamConnection $streamerConnection,
        string $channelName,
        string $operation
    ) {
        $this->channelName = $channelName;
        $this->operation = $operation;
        $this->streamerConnection = $streamerConnection;
        // enable heartbeat?
        if ($streamerConnection->getHeartbeat() > 0) {
            $this->heartbeatSender = new PCNTLHeartbeatSender($streamerConnection);
            $this->heartbeatSender->register();
        }
    }

    /**
     * AbstractStreamer destructor.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * @inheritDoc
     */
    public function getChannel(?int $id = null): AMQPChannel
    {
        // create new channel using the given stream connection
        return $this->streamerConnection->channel($id);
    }

    /**
     * @inheritDoc
     */
    public function disconnect(): bool
    {
        try {
            if (!$this->streamerConnection->isConnected()) {
                throw new AMQPConnectionClosedException();
            }
            if (isset($this->heartbeatSender)) {
                $this->heartbeatSender->unregister();
                unset($this->heartbeatSender);
            }
            if (isset($this->streamerConnection)) {
                $this->streamerConnection->close();
            }
        } catch (Throwable $reason) {
            // Fault-tolerant
            return false;
        }
        return true;
    }
}
