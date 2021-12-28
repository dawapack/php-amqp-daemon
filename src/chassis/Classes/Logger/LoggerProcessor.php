<?php

namespace DaWaPack\Chassis\Classes\Logger;

use DaWaPack\Chassis\Classes\DTO\Logger\ContextError;
use DaWaPack\Chassis\Classes\DTO\Logger\Record;
use Throwable;
use function DaWaPack\Chassis\Helpers\env;

class LoggerProcessor
{

    private const DATETIME_FORMAT = 'Y-m-d\TH:i:s.vP';

    /**
     * @var Record $record
     */
    private Record $record;

    /**
     * LoggerProcessor constructor.
     *
     * @param Record|null $record
     */
    public function __construct(?Record $record = null)
    {
        $this->record = is_null($record) ? $this->createDefaultRecord() : $record;
    }

    /**
     * @param array $loggerRecord
     *
     * @return array
     */
    public function __invoke(array $loggerRecord): array
    {
        $this->fillRecord($loggerRecord);
        return $this->record->toArray();
    }

    /**
     * @return Record
     */
    private function createDefaultRecord(): Record
    {
        $defaultRecord = [
            "timestamp" => "",
            "level" => "",
            "message" => "",
        ];

        // Origin
        $defaultRecord['origin'] = env("ORIGIN", "unknown");

        // Region
        $defaultRecord['region'] = env("REGION", "unknown");

        // Application name, environment & type
        $defaultRecord["application"] = [
            "name" => env(" APP_SYSNAME", null),
            "environment" => env(" APP_ENV", null),
            "type" => RUNNER_TYPE
        ];
        $defaultRecord["application"]["name"] ?? env("APP_SYSNAME", "unknown");
        $defaultRecord["application"]["environment"] ?? env("APP_ENV", null);

        // Component
        $defaultRecord['component'] = env(" APP_LOGCOMPONENT", "application_unhandled_exception");

        // Extra
        $defaultRecord["extra"] = [];

        // Context
        $defaultRecord["context"] = [];

        return new Record($defaultRecord);
    }

    /**
     * @param array $loggerRecord
     *
     * @return Record
     */
    private function fillRecord(array $loggerRecord): Record
    {
        // timestamp
        $this->record->timestamp = $loggerRecord["datetime"]->format(self::DATETIME_FORMAT);

        // log level
        $this->record->level = $loggerRecord["level_name"];

        // log message
        $this->record->message = $loggerRecord["message"];

        // component from context/component
        if (isset($loggerRecord["context"]["component"])) {
            $this->record->component = $loggerRecord["context"]["component"];
        }
        unset($loggerRecord["context"]["component"]);

        // extra from context/extra
        if (isset($loggerRecord["context"]["extra"])) {
            $this->record->extra = $loggerRecord["context"]["extra"];
        }
        unset($loggerRecord["context"]["extra"]);

        // Copy context
        $this->record->context = $loggerRecord["context"];

        // context/error
        if (
            isset($loggerRecord["context"]["error"])
            && $loggerRecord["context"]["error"] instanceof Throwable
        ) {
            $this->record->context["error"] = (new ContextError())
                ->fillFromThrowable($loggerRecord["context"]["error"])
                ->toArray();
        }

        return $this->record;
    }
}
