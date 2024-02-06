<?php

namespace App\Components\Transport\Protokol\Http;

use App\Components\Transport\Protokol\TransportInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface extends TransportInterface
{

    public function send(): ResponseInterface;

    public function sendAsync(): PromiseInterface;

    public function setHeader(string $name, string $value): self;

    public function setJwt(): self;

    /**
     * @param array<mixed> $query
     * @return self
     */
    public function setQuery(array $query): self;

    public function setUri(string $uri): self;

    public function setMethod(string $method): self;
}
