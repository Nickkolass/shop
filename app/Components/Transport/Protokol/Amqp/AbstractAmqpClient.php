<?php

namespace App\Components\Transport\Protokol\Amqp;

abstract class AbstractAmqpClient implements AmqpClientInterface
{

    /** @var array<string> $connection_cred */
    protected array $connection_cred;
    protected string $message;
    protected string $exchange;
    protected string $routing_key;
    protected string $reply_to;

    abstract protected function setConnection(): self;

    abstract protected function basicPublish(): self;

    abstract protected function basicConsume(string $queue, callable $callback): void;

    abstract protected function declare(string $queue, string $routing_key, ?string $exchange = null): self;

    abstract protected function unsetConnection(): self;

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setExchange(string $exchange): self
    {
        $this->exchange = $exchange;
        return $this;
    }

    public function setRoutingKey(string $routing_key): self
    {
        $this->routing_key = $routing_key;
        return $this;
    }

    /**
     * @param array<string> $connection_cred
     * @return self
     */
    public function setConnectionCred(array $connection_cred): self
    {
        $this->connection_cred = $connection_cred;
        return $this;
    }

    public function setReplyTo(string $reply_to): self
    {
        $this->reply_to = $reply_to;
        return $this;
    }

    public function publish(): void
    {
        $this->setConnection()
            ->basicPublish()
            ->unsetConnection();
    }

    public function consume(string $queue): void
    {
        $callback = $this->getCallback($queue);
        $this->setConnection()
            ->basicConsume($queue, $callback);
    }

    private function getCallback(string $queue): callable|array
    {
        $res = [];
        foreach ((array)glob('app/Components/Transport/Consumer/*/config.php') as $config) {
            $config = include $config;
            if ($config['options']['amqp']['queue'] == $queue) {
                $res = [$config['options']['amqp']['bind'], 'callback'];
            }
        }
        return $res;
    }

    public function init(string $queue, string $routing_key, ?string $exchange = null): void
    {
        $this->setConnection()
            ->declare($queue, $routing_key, $exchange)
            ->unsetConnection();
    }

    public function initConsumers(): void
    {
        foreach ((array)glob('app/Components/Transport/Consumer/*', GLOB_ONLYDIR) as $dir) {
            $dir_key = explode('/', (string)$dir);
            $dir_key = strtolower(array_pop($dir_key));
            $queue = config("consumers.$dir_key.options.amqp.queue");
            $routing_key = config("consumers.$dir_key.options.amqp.routing_key");
            $this->init($queue, $routing_key);
        }
    }
}
