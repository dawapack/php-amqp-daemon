<?php

namespace DaWaPack\Chassis\Classes\Logger;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use function DaWaPack\Chassis\Helpers\env;

class LoggerFactory
{

    private const DEFAULT_LOG_FILE_NAME = "php://stderr";

    /**
     * @var string $basePath
     */
    private string $basePath;

    /**
     * Nobody cares about implementation - we just need something to use to log things
     *
     * @param string $basePath
     *
     * @return LoggerInterface
     */
    public function __invoke(string $basePath): LoggerInterface
    {
        $this->basePath = $basePath;
        // Create a logger instance
        $logger = new Logger(env('APP_SYSNAME', 'unknown'));
        // Add a custom handler
        $handler = new StreamHandler(
            $this->getLogFileName(),
            Logger::toMonologLevel(env('APP_LOGLEVEL', LOGGER::DEBUG))
        );
        // Set a custom formatter
        $handler->setFormatter(new JsonFormatter());
        // Use a custom processor
        $processor = new LoggerProcessor();
        $handler->pushProcessor($processor);
        // Set logger handler
        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * @return string
     */
    private function getLogFileName(): string
    {
        $streamFromEnv = env('APP_LOGFILE', self::DEFAULT_LOG_FILE_NAME);
        return (preg_match('/^php:\/\/[a-z]+$/', $streamFromEnv) > 0
            ? $streamFromEnv
            : $this->getFileName($streamFromEnv)
        );
    }

    /**
     * @param string $streamFromEnv
     *
     * @return string
     */
    private function getFileName(string $streamFromEnv): string
    {
        $stream = $this->basePath . "/" . ltrim($streamFromEnv, "/");
        return (file_exists($stream) ? $stream : self::DEFAULT_LOG_FILE_NAME);
    }
}
