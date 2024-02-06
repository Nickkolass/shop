<?php

use App\Components\Transport\Protokol\Amqp\RabbitmqClient;
use App\Components\Transport\Protokol\Http\GuzzleClient;

return [

    'protocols' => [
        'amqp' => [
            'default' => 'rabbitmq',
            'clients' => [
                'rabbitmq' => [
                    'bind' => RabbitmqClient::class,
                    'connection' => [
                        'host' => env('RABBITMQ_HOST'),
                        'port' => env('RABBITMQ_PORT'),
                        'user' => env('RABBITMQ_DEFAULT_USER'),
                        'password' => env('RABBITMQ_DEFAULT_PASS'),
                    ],
                ],
            ],
        ],

        'http' => [
            'default' => 'guzzle',
            'clients' => [
                'guzzle' => [
                    'bind' => GuzzleClient::class,
                    'options' => [
                        'base_uri' => env('GUZZLE_HOST', 'host.docker.internal') . ':' . env('APP_PORT', '8876'),
                        'timeout' => '10.0',
                        'verify' => false,
                    ],
                ],
            ],
        ],
    ],
];


