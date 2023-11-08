<?php

namespace App\Components\HttpClient\Guzzle;

use App\Components\HttpClient\HttpClientInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class GuzzleClient implements HttpClientInterface
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client(config('services.guzzle'));
    }

    /**
     * @param string $method
     * @param UriInterface|string $uri
     * @param array<mixed> $options
     * @return ResponseInterface
     */
    public function request(string $method, UriInterface|string $uri = '', array $options = []): ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }
}
