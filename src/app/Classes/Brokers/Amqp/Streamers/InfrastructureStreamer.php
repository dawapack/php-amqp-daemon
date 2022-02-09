<?php

declare(strict_types=1);

namespace DaWaPack\Classes\Brokers\Amqp\Streamers;

class InfrastructureStreamer extends AbstractStreamer
{
    /**
     * @param bool $declareBindings
     *
     * @return int
     */
    public function brokerChannelsSetup(bool $declareBindings = true): int
    {
        $channels = $this->contractsManager->getChannels();
        foreach ($channels as $channel) {
            $this->channelDeclare($channel, $declareBindings);
        }
        return $channels->count();
    }

    /**
     * @return int
     */
    public function brokerChannelsClear(): int
    {
        $channels = $this->contractsManager->getChannels();
        foreach ($channels as $channel) {
            $this->channelDelete($channel);
        }
        return $channels->count();
    }

    /**
     * @param string|null $filter - 'exchanges' or 'queues'
     *
     * @return array
     */
    public function getAvailableChannels(?string $filter = null): array
    {
        return isset($filter) && isset($this->availableChannels[$filter])
            ? $this->availableChannels[$filter]
            : $this->availableChannels;
    }
}
