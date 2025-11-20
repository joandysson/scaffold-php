<?php

declare(strict_types=1);

namespace App\Config\Router;

class RouteMiddlewareRegistrar
{
    /** @var array<int, callable> */
    private array $middlewares;

    /**
     * @param array<int, callable|string> $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = array_map([Router::class, 'normalizeMiddleware'], $middlewares);
    }

    public function get(string $route, callable|string $handler, ?string $name = null): void
    {
        Router::get($route, $handler, $name, $this->middlewares);
    }

    public function post(string $route, callable|string $handler, ?string $name = null): void
    {
        Router::post($route, $handler, $name, $this->middlewares);
    }

    public function put(string $route, callable|string $handler, ?string $name = null): void
    {
        Router::put($route, $handler, $name, $this->middlewares);
    }

    public function patch(string $route, callable|string $handler, ?string $name = null): void
    {
        Router::patch($route, $handler, $name, $this->middlewares);
    }

    public function delete(string $route, callable|string $handler, ?string $name = null): void
    {
        Router::delete($route, $handler, $name, $this->middlewares);
    }
}
