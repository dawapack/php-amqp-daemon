<?php
declare(strict_types=1);

namespace DaWaPack\Classes\Threads;

use DaWaPack\Chassis\Application;
use DaWaPack\Classes\Threads\Configuration\ThreadConfiguration;
use DaWaPack\Classes\Threads\DTO\JobsProcessed;
use DaWaPack\Interfaces\ThreadInstanceInterface;
use DaWaPack\Kernel;
use parallel\Channel;
use parallel\Future;
use parallel\Runtime;
use Ramsey\Uuid\Uuid;
use function DaWaPack\Chassis\Helpers\app;

class ThreadInstance implements ThreadInstanceInterface
{

    private ThreadConfiguration $threadConfiguration;
    private JobsProcessed $jobsProcessed;
    private Runtime $runtime;
    private Future $future;
    private Channel $channel;

    private float $loadAverage = 0;
    private bool $abortRequested = false;

    /**
     * @inheritDoc
     */
    public function setConfiguration(ThreadConfiguration $threadConfiguration): void
    {
        $this->threadConfiguration = $threadConfiguration;
    }

    /**
     * @inheritDoc
     */
    public function getConfiguration(?string $key = null)
    {
        return !is_null($key)
            ? $this->threadConfiguration->{$key} ?? null
            : $this->threadConfiguration->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getFuture(): Future
    {
        return $this->future;
    }

    /**
     * @inheritDoc
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function spawn(): string
    {
        $uuid = (Uuid::uuid4())->toString();
        // Create parallel runtime - inject vendor autoload as bootstrap
        $basePath = app('basePath');
        // Create parallel future
        $this->future = (new Runtime($basePath . "/vendor/autoload.php"))->run(
            static function (string $threadId, string $basePath): void {
                // Define application in Closure as worker
                define('RUNNER_TYPE', 'worker');
                /** @var Application $app */
                $app = require $basePath . '/bootstrap/app.php';
                $app->withConfig("broker");
                // Start processing jobs
                (new Kernel($app))->boot($threadId);
            }, [$uuid, $basePath]
        );
        return $uuid;
    }

    /**
     * @param string|null $reason
     *
     * @return bool
     */
    public function respawn(?string $reason = null): bool
    {
        return true;
    }

    /**
     * Request aborting this instance
     *
     * @return void
     */
    public function cancel(): void
    {
        $this->abortRequested = true;
    }
}
