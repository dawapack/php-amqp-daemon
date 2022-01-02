<?php
declare(strict_types=1);

namespace DaWaPack\Providers;

use DaWaPack\Classes\Threads\ThreadInstance;
use DaWaPack\Interfaces\ThreadInstanceInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ThreadInstanceServiceProvider extends AbstractServiceProvider
{

    public function provides(string $id): bool
    {
        return $id === ThreadInstanceInterface::class;
    }

    public function register(): void
    {
        // add key/value pair
        $this->getContainer()
            ->add(ThreadInstanceInterface::class, ThreadInstance::class)
            ->setShared(false);
    }
}
