<?php
declare(strict_types=1);

namespace DaWaPack\Bootstrap;

use DaWaPack\Chassis\Application;
use DaWaPack\Chassis\Bootstrap\LoadEnvironmentVariables;
use function DaWaPack\Chassis\Helpers\env;
use function DaWaPack\Chassis\Helpers\defineRunner;

// import PSR autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Define runner globals
defineRunner();

// Set ENV base path
$basePath = dirname(__DIR__);

// Set default timezone
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

// Load environment variables
(new LoadEnvironmentVariables($basePath))->bootstrap();

// Create & return application instance
return new Application($basePath);
