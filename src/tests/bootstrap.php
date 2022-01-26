<?php
declare(strict_types=1);

use DaWaPack\Chassis\Application;
use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfiguration;
use DaWaPack\Classes\Brokers\Amqp\Configurations\BrokerConfigurationInterface;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManager;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsManagerInterface;
use DaWaPack\Classes\Brokers\Amqp\Contracts\ContractsValidator;
use DaWaPack\Classes\Brokers\Amqp\Streamers\SubscriberStreamer;
use DaWaPack\Classes\Brokers\Amqp\Streamers\SubscriberStreamerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Log\LoggerInterface;

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';
$brokerConfigurationFixture = require __DIR__ . "/app/Brokers/Amqp/Fixtures/Config/broker.php";
$app->add(BrokerConfigurationInterface::class, BrokerConfiguration::class)
    ->addArgument($brokerConfigurationFixture);

$app->add(ContractsManagerInterface::class, function ($app) {
    return new ContractsManager(
        $app->get(BrokerConfigurationInterface::class),
        new ContractsValidator()
    );
})->addArgument($app);

$app->add('broker-streamer', function ($app) {
    return new AMQPStreamConnection(
        ...array_values($app->get(ContractsManagerInterface::class)->toStreamConnectionFunctionArguments())
    );
})->addArgument($app)->setShared(false);

$app->add(SubscriberStreamerInterface::class, function ($app){
    return new SubscriberStreamer(
        $app->get('broker-streamer'),
        $app->get(ContractsManagerInterface::class),
        $app->get(LoggerInterface::class)
    );
})->addArgument($app)->setShared(false);
