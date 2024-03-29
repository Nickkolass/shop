<?php

namespace App\Components\Transport\Protokol\Http;

abstract class AbstractHttpClient implements HttpClientInterface
{
    /** @var array<mixed> */
    protected array $query = [];
    /** @var array<mixed> */
    protected array $headers = [];
    protected ?string $uri = null;
    protected ?string $method = null;

    /**
     * @param array<mixed> $query
     * @return self
     */
    public function setQuery(array $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers += [$name => $value];
        return $this;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function setJwt(): self
    {
        /** @var string $jwt */
        $jwt = request()->cookie('jwt');
        if ($jwt) $this->setHeader('Authorization', $jwt);
        return $this;
    }
}
