<?php
declare(strict_types=1);

namespace DaWaPack\Bootstrap;

use DaWaPack\Chassis\Application;
use DaWaPack\Chassis\Bootstrap\LoadEnvironmentVariables;
use function DaWaPack\Chassis\Helpers\env;

require_once __DIR__ . '/../vendor/autoload.php';

// Set ENV base path
$basePath = dirname(__DIR__);

// Set default timezone
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

// Load environment variables
(new LoadEnvironmentVariables($basePath))->bootstrap();

// Create & return application instance
return new Application($basePath);
