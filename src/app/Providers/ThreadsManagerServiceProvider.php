<?php

namespace DaWaPack\Providers;

use DaWaPack\Classes\Threads\ThreadsManager;
use DaWaPack\Interfaces\ThreadsConfigurationInterface;
use DaWaPack\Interfaces\ThreadsManagerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ThreadsManagerServiceProvider extends AbstractServiceProvider
{

    public function provides(string $id): bool
    {
        // list of services provided by this service provider
        $services = [
            ThreadsManagerInterface::class => ThreadsManager::class,
        ];
        return isset($services[$id]);
    }

    public function register(): void
    {
        // Instantiate ThreadsManager
        $this->getContainer()
            ->add(ThreadsManagerInterface::class, ThreadsManager::class)
            ->addArgument(ThreadsConfigurationInterface::class);
    }
}
