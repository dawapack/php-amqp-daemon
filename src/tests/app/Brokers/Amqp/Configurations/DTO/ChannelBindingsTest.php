<?php

declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Configurations\DTO;

use DaWaPack\Tests\AppTestCase;
use Spatie\DataTransferObject\DataTransferObjectError;

class ChannelBindingsTest extends AppTestCase
{
    /**
     * @return void
     */
    public function testSutMustThrowAnExceptionInitializingDtoWithEmptyParameters(): void
    {
        $this->expectException(DataTransferObjectError::class);
        new \DaWaPack\Chassis\Framework\Brokers\Amqp\Configurations\DataTransferObject\ChannelBindings([]);
    }
}
