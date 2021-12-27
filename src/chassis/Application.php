<?php

namespace DaWaPack\Chassis;

use DaWaPack\Chassis\Classes\Logger\LoggerFactory;
use DaWaPack\Chassis\Support\ErrorsHandler;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Log\LoggerInterface;

class Application extends Container
{

    use ErrorsHandler;

    private string $basePath;

    public function __construct(string $basePath = null)
    {
        parent::__construct();
        $this->basePath = $basePath;

        $this->enableAutoWiring();
        $this->bootstrapContainer();
        $this->registerErrorHandling();
    }

    protected function bootstrapContainer(): void
    {
        $this->add(LoggerInterface::class, new LoggerFactory($this->basePath));
    }

    private function enableAutoWiring(): void
    {
        $this->delegate(new ReflectionContainer(true));
    }

    /**
     * Single entry point of application
     */
    public function run(): void
    {
        $logger = $this->get(LoggerInterface::class);
        $logger->info(
            "Message", ["component" => "blabla"]
        );
        do {
            sleep(600);
        } while (true);

    }
}
