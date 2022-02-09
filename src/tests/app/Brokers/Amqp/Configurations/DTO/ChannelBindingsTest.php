<?php

declare(strict_types=1);

namespace DaWaPack\Tests\app\Brokers\Amqp\Configurations\DTO;

use DaWaPack\Classes\Brokers\Amqp\Configurations\DTO\ChannelBindings;
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
        new ChannelBindings([]);
    }
}