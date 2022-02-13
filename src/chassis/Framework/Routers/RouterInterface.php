<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Routers;

use DaWaPack\Chassis\Framework\Brokers\Amqp\MessageBags\MessageBagInterface;

interface RouterInterface
{
    /**
     * @param MessageBagInterface $messageBag
     *
     * @return bool
     */
    public function route(MessageBagInterface $messageBag): bool;
}
