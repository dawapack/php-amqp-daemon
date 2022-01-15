<?php
declare(strict_types=1);

namespace DaWaPack\Tests;

use DaWaPack\Chassis\Application;
use PHPUnit\Framework\TestCase;

class AppTestCase extends TestCase
{
    protected Application $app;

    public function __construct()
    {
        parent::__construct();
        $this->app = require __DIR__ . '/../bootstrap/app.php';
    }
}
