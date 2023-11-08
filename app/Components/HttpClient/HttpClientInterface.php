<?php

namespace App\Components\HttpClient;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface HttpClientInterface
{
    /**
     * @param string $method
     * @param UriInterface|string $uri
     * @param array<mixed> $options
     * @return ResponseInterface
     */
    public function request(string $method, UriInterface|string $uri, array $options): ResponseInterface;
}
