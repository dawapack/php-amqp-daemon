<?php
declare(strict_types=1);

namespace DaWaPack\Chassis\Classes\DTO\Logger;

use Spatie\DataTransferObject\DataTransferObject;
use Throwable;

class ContextError extends DataTransferObject
{

    /**
     * @var string
     */
    public string $file = "";

    /**
     * @var int
     */
    public int $line = 0;

    /**
     * @var string
     */
    public string $message = "";

    /**
     * @var string
     */
    public string $trace = "";

    /**
     * Fill DTO properties from throwable
     *
     * @param Throwable $throwable
     *
     * @return ContextError
     */
    public function fillFromThrowable(Throwable $throwable): ContextError
    {
        $this->message = $throwable->getMessage();
        $this->file = $throwable->getFile();
        $this->line = $throwable->getLine();
        $this->trace = $throwable->getTraceAsString();

        return $this;
    }
}
