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
            'heartbeat' => 30,
            'connection_timeout' => 5.0,
            'read_write_timeout' => 30.0,
            'channel_rpc_timeout' => 30.0,
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
                'source' => '/var/www/vendor/dawapack/asyncapi-contract-sample/src',
                'validator' => '/var/www/vendor/dawapack/asyncapi-contract-sample/src/json-schemas/bindings/amqp'
            ],
            'definitions' => [
                'contract' => 'dawapack.yml',
                'infrastructure' => 'infrastructure.yml'
            ]
        ]
    ]
];
