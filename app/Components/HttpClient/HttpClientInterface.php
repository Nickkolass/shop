<?php

namespace App\Components\HttpClient;

use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public function send(): ResponseInterface;

    public function setHeader(string $name, string $value): self;

    /**
     * @param array<mixed> $query
     * @return self
     */
    public function setQuery(array $query): self;

    public function setUri(string $uri): self;

    public function setMethod(string $method): self;
}
