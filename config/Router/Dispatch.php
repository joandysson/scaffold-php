<?php
declare(strict_types=1);

namespace Config\Router;

use Config\Response\HttpStatus;
use Config\Response\Response;
use Config\Request\Request;
use RuntimeException;

abstract class Dispatch
{
    protected static string $httpMethod;
    protected static array $routes = [];
    protected static ?array $route = null;
    protected static string $patch;
    protected static ?string $projectUrl = null;
    protected static string $separator;
    protected static ?string $group = null;
    protected static ?int $error = null;
    protected static array $middlewares = [];
    protected static bool $dispatchOnRegistration = false;
    /** @var array<string, bool> */
    protected static array $registeredMethods = [];

    public const BAD_REQUEST = HttpStatus::BAD_REQUEST->value;
    public const NOT_FOUND = HttpStatus::NOT_FOUND->value;
    public const METHOD_NOT_ALLOWED = HttpStatus::METHOD_NOT_ALLOWED->value;
    public const NOT_IMPLEMENTED = HttpStatus::NOT_IMPLEMENTED->value;

    public function __construct()
    {
        self::$patch = explode('?', $_SERVER['REQUEST_URI'])[0];
        self::$separator = ':';
        self::$httpMethod = $_SERVER['REQUEST_METHOD'];
    }

    public function __debugInfo(): array
    {
        return self::$routes;
    }

    public static function group(?string $group = null): ?string
    {
        return self::$group = ($group ? str_replace('/', '', $group) : null);
    }

    public static function error(): ?int
    {
        return self::$error;
    }

    public static function addMiddleware(string|callable $middleware): void
    {
        self::$middlewares[] = self::normalizeMiddleware($middleware);
    }

    public static function normalizeMiddleware(string|callable $middleware): callable
    {
        return is_string($middleware) ? new $middleware() : $middleware;
    }

    public static function run(): bool
    {
        self::$httpMethod = $_SERVER['REQUEST_METHOD'];
        self::$patch = explode('?', $_SERVER['REQUEST_URI'])[0];

        if (
            (empty(self::$routes) || empty(self::$routes[self::$httpMethod]))
            && empty(self::$registeredMethods[self::$httpMethod])
        ) {
            self::$error = self::NOT_IMPLEMENTED;
            return false;
        }

        self::$route = null;
        foreach (self::$routes[self::$httpMethod] ?? [] as $key => $route) {
            $matches = self::matchesRoute($key);
            if ($matches === null) {
                continue;
            }

            self::$route = self::withRouteData($route, $matches);
            return self::execute();
        }

        return self::execute();
    }

    protected static function dispatchDuringRegistration(string $method, string $route, array $routeItem): bool
    {
        if (!self::$dispatchOnRegistration) {
            return false;
        }

        self::$registeredMethods[$method] = true;

        if ($method !== self::$httpMethod) {
            return false;
        }

        $matches = self::matchesRoute($route);
        if ($matches === null) {
            return false;
        }

        self::$route = self::withRouteData($routeItem, $matches);
        return self::execute();
    }

    /**
     * @return array<int, string>|null
     */
    private static function matchesRoute(string $route): ?array
    {
        if (!preg_match('~^' . $route . '$~', self::$patch, $matches)) {
            return null;
        }

        array_shift($matches);

        return $matches;
    }

    /**
     * @param array<string, mixed> $route
     * @param array<int, string> $matches
     * @return array<string, mixed>
     */
    private static function withRouteData(array $route, array $matches): array
    {
        $params = [];

        foreach ($route['parameterNames'] ?? [] as $index => $name) {
            $params[$name] = $matches[$index] ?? null;
        }

        $route['data'] = $params;

        return $route;
    }

    protected static function execute(): bool
    {
        if (self::$route) {
            if (is_callable(self::$route['handler'])) {
                $params = make(self::$route['handler'], self::$route['data'] ?? []);
                $request = null;
                foreach ($params as $p) {
                    if ($p instanceof Request) {
                        $request = $p;
                        break;
                    }
                }
                if ($request === null) {
                    $request = new Request();
                    $request->setRouteParams(self::$route['data'] ?? []);
                }
                foreach (self::middlewaresForRoute() as $middleware) {
                    $middlewareResult = $middleware($request);
                    if ($middlewareResult instanceof Response) {
                        $middlewareResult->send();
                        return true;
                    }
                }
                $result = call_user_func_array(self::$route['handler'], $params);
                return self::emit($result);
            }

            $controller = self::$route['handler'];
            $method = self::$route['action'];

            if (!class_exists($controller)) {
                self::$error = self::BAD_REQUEST;
                throw new RuntimeException("Controller {$controller} not found");
            }

            $newController = new $controller();

            if (!method_exists($controller, $method)) {
                self::$error = self::METHOD_NOT_ALLOWED;
                throw new RuntimeException("Method {$method} not found in {$controller}");
            }

            $params = make([$newController, $method], self::$route['data'] ?? []);
            $request = null;
            foreach ($params as $p) {
                if ($p instanceof Request) {
                    $request = $p;
                    break;
                }
            }
            if ($request === null) {
                $request = new Request();
                $request->setRouteParams(self::$route['data'] ?? []);
            }
            foreach (self::middlewaresForRoute() as $middleware) {
                $middlewareResult = $middleware($request);
                if ($middlewareResult instanceof Response) {
                    $middlewareResult->send();
                    return true;
                }
            }
            $result = $newController->$method(...$params);
            return self::emit($result);
        }

        self::$error = self::NOT_FOUND;
        return false;
    }

    /**
     * @return callable[]
     */
    private static function middlewaresForRoute(): array
    {
        $routeMiddlewares = self::$route['middlewares'] ?? [];

        return array_merge(self::$middlewares, $routeMiddlewares);
    }

    private static function emit(mixed $result): bool
    {
        if ($result instanceof Response) {
            $result->send();
            return true;
        }
        if (is_array($result)) {
            $response = (new Response())->json($result, HttpStatus::OK);
            $response->send();
            return true;
        }
        if (is_string($result)) {
            $response = new Response();
            $response->send($result, HttpStatus::OK);
            return true;
        }
        return true;
    }
}
