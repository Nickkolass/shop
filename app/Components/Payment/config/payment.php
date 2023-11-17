<?php

return [

    'default' => env('PAYMENT_CONNECTION', 'stub'),

    'connections' => [
        'stub' => [
            'bind' => \App\Components\Payment\src\Clients\PaymentClientStub::class,
            'shop' => [
                'login' => 'stub',
                'password' => 'stub'
            ],
            'agent' => [
                'login' => 'stub',
                'password' => 'stub'
            ],
        ],

        'yookassa' => [
            'bind' => \App\Components\Payment\src\Clients\YooKassaClient::class,
            'shop' => [
                'login' => env('YOO_KASSA_SHOP_ID'),
                'password' => env('YOO_KASSA_SHOP_TOKEN')
            ],
            'agent' => [
                'login' => env('YOO_KASSA_AGENT_ID'),
                'password' => env('YOO_KASSA_AGENT_TOKEN')
            ],
        ],
    ],
];
