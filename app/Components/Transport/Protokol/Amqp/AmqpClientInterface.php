<?php

namespace App\Components\Transport\Protokol\Amqp;

use App\Components\Transport\Protokol\TransportInterface;

interface AmqpClientInterface extends TransportInterface
{

    const EXCHANGE = 'shop';
    const QUEUE = 'shop';
    const ROUTING_KEY = 'shop';

    public function setMessage(string $message): self;

    public function setExchange(string $exchange): self;

    public function setRoutingKey(string $routing_key): self;

    /**
     * @param array<string> $connection_cred
     * @return self
     */
    public function setConnectionCred(array $connection_cred): self;

    public function setReplyTo(string $reply_to): self;

    public function consume(string $queue): void;

    public function init(string $queue, string $routing_key, ?string $exchange = null): void;

    public function initConsumers(): void;
}
