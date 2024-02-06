<?php

namespace App\Components\Transport\Protokol\Amqp;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqClient extends AbstractAmqpClient
{

    protected AMQPStreamConnection $connection;

    protected AMQPChannel $channel;


    protected function setConnection(): self
    {
        $this->connection = new AMQPStreamConnection(...config('transport.protocols.amqp.clients.rabbitmq.connection'));
        $this->channel = $this->connection->channel();
        return $this;
    }

    protected function unsetConnection(): self
    {
        $this->channel->close();
        $this->connection->close();
        unset($this->channel, $this->connection);
        return $this;
    }

    protected function basicPublish(): self
    {
        $this->channel->basic_publish(
            new AMQPMessage($this->message, ['reply_to' => $this->reply_to]),
            $this->exchange,
            $this->routing_key);
        return $this;
    }

    protected function basicConsume(string $queue, callable $callback): void
    {
        $this->channel->basic_consume($queue, no_ack: true, callback: $callback);
        $this->channel->consume();
    }

    public function declare(string $queue, string $routing_key, ?string $exchange = null): self
    {
        if ($exchange) $this->channel->exchange_declare($exchange, 'direct', false, true, false);
        $this->channel->queue_declare($queue, durable: true, auto_delete: false);
        $this->channel->queue_bind($queue, self::EXCHANGE, $routing_key);
        return $this;
    }
}
