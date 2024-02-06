<?php

namespace App\Components\Transport\Consumer;

use App\Components\Transport\Protokol\Amqp\AmqpClientInterface;
use App\Components\Transport\Protokol\Http\HttpClientInterface;

abstract class AbstractConsumerTransport
{

    protected function getHttpTransport(): HttpClientInterface
    {
        return app(HttpClientInterface::class);
    }

    protected function getAmqpTransport(): AmqpClientInterface
    {
        return app(AmqpClientInterface::class);
    }
}
