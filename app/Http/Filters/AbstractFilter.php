<?php

namespace App\Http\Filters;

use Illuminate\Contracts\Database\Query\Builder;

abstract class AbstractFilter implements FilterInterface
{

    /** @param array<mixed> $queryParams */
    public function __construct(private array $queryParams)
    {
    }

    public function apply(Builder $builder): void
    {
        $this->before($builder);

        foreach ($this->getCallbacks() as $name => $callback) {
            if (isset($this->queryParams[$name])) {
                call_user_func($callback, $builder, $this->queryParams[$name]);
            }
        }
    }

    protected function before(Builder $builder): void
    {
        $this->queryParams = array_filter($this->queryParams);
    }

    /** @return array<string, callable>> */
    abstract protected function getCallbacks(): array;

    protected function getQueryParam(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    protected function removeQueryParam(string ...$keys): AbstractFilter
    {
        foreach ($keys as $key) unset($this->queryParams[$key]);
        return $this;
    }
}
