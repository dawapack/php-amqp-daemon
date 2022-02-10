<?php

declare(strict_types=1);

namespace DaWaPack\Providers;

use DaWaPack\Classes\Threads\ThreadInstance;
use DaWaPack\Classes\Threads\ThreadInstanceInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ThreadInstanceServiceProvider extends AbstractServiceProvider
{
    /**
     * @param string $id
     *
     * @return bool
     */
    public function provides(string $id): bool
    {
        return $id === ThreadInstanceInterface::class;
    }

    /**
     * @return void
     */
    public function register(): void
    {
        // add key/value pair
        $this->getContainer()
            ->add(ThreadInstanceInterface::class, ThreadInstance::class)
            ->setShared(false);
    }
}
