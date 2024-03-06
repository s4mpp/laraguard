<?php

namespace S4mpp\Laraguard\Traits;

trait HasMiddleware
{
    /**
     * @var array<mixed>
     */
    private array $middlewares = [];

	/**
     * @param  array<mixed> $middlewares
     */
    public function middleware(...$middlewares): self
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
