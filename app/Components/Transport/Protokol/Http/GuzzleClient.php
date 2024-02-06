<?php

namespace App\Components\Transport\Protokol\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleClient extends AbstractHttpClient
{

    public Client $client;

    public function __construct()
    {
        $this->client = new Client(config('transport.protocols.http.clients.guzzle.options'));
    }

    public function publish(): void
    {
        $this->send();
    }

    public function send(): ResponseInterface
    {
        /** @var ResponseInterface */
        return $this->request();
    }

    public function sendAsync(): PromiseInterface
    {
        /** @var PromiseInterface */
        return $this->request(true);
    }

    private function request(bool $async = false): ResponseInterface|PromiseInterface
    {
        $this->validateProps();
        $options = ['headers' => $this->headers, 'query' => $this->query];
        $method = $async ? 'requestAsync' : 'request';
        $res = $this->client->$method($this->method, $this->uri, $options);
        $this->unsetProps();
        return $res;
    }

    private function validateProps(): void
    {
        if (!$this->uri || !$this->method) {
            throw new InvalidArgumentException('Неверный метод запроса или uri', 400);
        }
    }

    private function unsetProps(): void
    {
        unset($this->method, $this->uri, $this->headers, $this->query);
    }
}
