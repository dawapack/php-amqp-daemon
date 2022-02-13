<?php
declare(strict_types=1);

namespace DaWaPack\Bootstrap;

use DaWaPack\Chassis\Application;
use DaWaPack\Chassis\Framework\Loaders\Environment;

use function DaWaPack\Chassis\Helpers\defineRunner;
use function DaWaPack\Chassis\Helpers\env;

// import PSR autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Define runner globals
defineRunner();

// Set ENV base path
$basePath = dirname(__DIR__);

// Load environment variables
(new Environment($basePath))->bootstrap();

// Set default timezone
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

// Create & return application instance
return new Application($basePath);
