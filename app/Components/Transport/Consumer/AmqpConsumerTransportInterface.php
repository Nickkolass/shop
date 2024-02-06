<?php

namespace App\Components\Transport\Consumer;

interface AmqpConsumerTransportInterface
{
    public static function callback(mixed $message): void;
}
