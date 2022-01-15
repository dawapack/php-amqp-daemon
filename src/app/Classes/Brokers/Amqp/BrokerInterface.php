<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp;

use DaWaPack\Classes\Brokers\Amqp\Streamers\StreamerInterface;

interface BrokerInterface
{
    /**
     * @return string|null
     */
    public function getChannel(): ?string;

    /**
     * @return string|null
     */
    public function getOperation(): ?string;

    /**
     * @return StreamerInterface|null
     */
    public function streamer(): ?StreamerInterface;

    /**
     * @param StreamerInterface $streamer
     *
     * @return $this
     */
    public function setStreamer(StreamerInterface $streamer): self;
}
