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
                'source' => '/vendor/dawapack/asyncapi-contract-sample/src',
                'validator' => '/json-schema/bindings/amqp'
            ],
            'definitions' => [
                'contract' => 'dawapack.yml',
                'infrastructure' => 'infrastructure.yml'
            ]
        ]
    ]
];
