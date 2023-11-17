<?php

namespace App\Components\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class GuzzleClient extends AbstractHttpClient
{

    public Client $client;

    public function __construct()
    {
        $this->client = new Client(config('services.guzzle'));
    }

    /**
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function send(): ResponseInterface
    {
        $this->setJwt();
        $this->validateProps();
        $options = ['headers' => $this->headers, 'query' => $this->query];
        $res = $this->client->request($this->method, $this->uri, $options);
        $this->unsetProps();
        return $res;
    }

    public function setJwt(): void
    {
        /** @var string $jwt */
        $jwt = request()->cookie('jwt');
        if ($jwt) $this->setHeader('Authorization', $jwt);
    }

    public function validateProps(): void
    {
        if (!$this->uri || !$this->method) {
            throw new InvalidArgumentException('Неверный метод запроса или uri', 400);
        }
    }

    public function unsetProps(): void
    {
        unset($this->method, $this->uri, $this->headers, $this->query);
    }
}
