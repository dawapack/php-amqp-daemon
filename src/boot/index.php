<?php
declare(strict_types = 1);

namespace DaWaPack\Boot;

use DaWaPack\Chassis\Application;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
*/
/** @var Application $app */
$app = require __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/
$app->run();