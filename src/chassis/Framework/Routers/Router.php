<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Framework\Routers;

use DaWaPack\Chassis\Framework\Brokers\Amqp\MessageBags\MessageBagInterface;
use DaWaPack\Chassis\Framework\Routers\Exceptions\RouteNotFoundException;

class Router implements RouterInterface
{
    private RouteDispatcher $dispatcher;
    private array $routes;

    /**
     * @param RouteDispatcher $dispatcher
     * @param array $routes
     */
    public function __construct(
        RouteDispatcher $dispatcher,
        array $routes
    ) {
        $this->dispatcher = $dispatcher;
        $this->routes = $routes;
    }

    /**
     * @inheritDoc
     */
    public function route(MessageBagInterface $messageBag): bool
    {
        if (!isset($this->routes[$messageBag->getProperty("type")])) {
            throw new RouteNotFoundException(
                sprintf("no route for message type '%s'", $messageBag->getProperty("type"))
            );
        }
        return $this->dispatcher->dispatch($this->routes[$messageBag->getProperty("type")], $messageBag);
    }
}
