<?php

namespace DaWaPack\Chassis\Framework\Providers;

use DaWaPack\Chassis\Framework\Routers\RouteDispatcher;
use DaWaPack\Chassis\Framework\Routers\Router;
use DaWaPack\Chassis\Framework\Routers\RouterInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

use function DaWaPack\Chassis\Helpers\app;

abstract class RoutingServiceProvider extends AbstractServiceProvider
{
    protected array $routes = [];

    public function provides(string $id): bool
    {
        return $id === RouterInterface::class;
    }

    public function register(): void
    {
        $this->getContainer()
            ->add(RouterInterface::class, Router::class)
            ->addArguments([
                new RouteDispatcher(app()),
                $this->routes
            ])->setShared(false);
    }
}
