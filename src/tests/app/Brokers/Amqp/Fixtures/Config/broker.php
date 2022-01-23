<?php

return [
    'connection' => 'amqp',
    'contract' => 'asyncapi',
    'connections' => [
        'default' => [
            'protocol' => 'https',
            'host' => 'localhost',
            'port' => 443,
            'user' => 'guest',
            'pass' => 'guest',
        ],
        'amqp' => [
            'protocol' => 'amqp',
            'host' => 'rabbitmq',
            'port' => 5672,
            'user' => 'guest',
            'pass' => 'guest',
            'vhost' => '/',
            'heartbeat' => 10,
        ]
    ],
    'contracts' => [
        'openapi' => [
            'driver' => 'filesystem',
            'paths' => [
                'source' => '',
                'validator' => ''
            ],
            'definitions' => [
                'contract' => ''
            ]
        ],
        'asyncapi' => [
            'driver' => 'filesystem',
            'paths' => [
                'source' => '/var/www/tests/app/Brokers/Amqp/Fixtures/Contract',
                'validator' => '/var/www/tests/app/Brokers/Amqp/Fixtures/Contract/json-schemas/bindings/amqp'
            ],
            'definitions' => [
                'contract' => 'tests-dawapack.yml',
                'infrastructure' => 'tests-infrastructure.yml'
            ]
        ]
    ]
];
