<?php

namespace DaWaPack\Chassis;

use DaWaPack\Chassis\Support\ErrorsHandler;
use Psr\Log\LoggerInterface;

class Application
{

    use ErrorsHandler;

    /**
     * @var string
     */
    private string $basePath;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Application constructor.
     *
     * @param LoggerInterface $logger
     * @param string|null $basePath
     */
    public function __construct(LoggerInterface $logger, string $basePath = null)
    {
        $this->logger = $logger;
        $this->basePath = $basePath;

        $this->bootstrapContainer();
        $this->registerErrorHandling();
    }

    /**
     * @return void
     */
    protected function bootstrapContainer(): void
    {
        // TODO: implement container stuffs here
    }

    /**
     * Set logger interface
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Single entry point of application
     *
     * @return void
     */
    public function run(): void
    {
        $this->logger->info(
            "Message", ["component" => "blabla"]
        );
        do {
            sleep(600);
        } while (true);

    }
}
