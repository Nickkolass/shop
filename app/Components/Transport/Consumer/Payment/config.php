<?php

use App\Components\Transport\Consumer\Payment\AmqpPaymentTransport;
use App\Components\Transport\Consumer\Payment\HttpPaymentTransport;

return [

    'requester_id' => env('PAYMENT_SHOP_ID'),
    'transport' => env('PAYMENT_TRANSPORT'),
    'callback_transport' => env('PAYMENT_CALLBACK_TRANSPORT'),
    'manual_payment' => true,

    'options' => [
        'http' => [
            'bind' => HttpPaymentTransport::class,
            'urls' => [
                'get_widget' => env('PAYMENT_URL') . '/api/payment/card/widget',
                'card_validate' => env('PAYMENT_URL') . '/api/payment/card/validate',
                'payment' => env('PAYMENT_URL') . '/api/payment',
            ],
        ],
        'amqp' => [
            'bind' => AmqpPaymentTransport::class,
            'queue' => 'payment',
            'routing_key' => 'payment',
        ]
    ],
];


