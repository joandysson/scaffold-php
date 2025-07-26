<?php
declare(strict_types=1);

namespace App\Config\Router;

use App\Config\Response\HttpStatus;
use App\Config\Request\Request;
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

    public static function addMiddleware(callable $middleware): void
    {
        self::$middlewares[] = $middleware;
    }

    public static function run(): bool
    {
        self::$httpMethod = $_SERVER['REQUEST_METHOD'];
        self::$patch = explode('?', $_SERVER['REQUEST_URI'])[0];

        if (empty(self::$routes) || empty(self::$routes[self::$httpMethod])) {
            self::$error = self::NOT_IMPLEMENTED;
            return false;
        }

        self::$route = null;
        foreach (self::$routes[self::$httpMethod] as $key => $route) {
            if (preg_match('~^' . $key . '$~', self::$patch)) {
                self::$route = $route;
            }
        }

        return self::execute();
    }

    private static function execute(): bool
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
                foreach (self::$middlewares as $middleware) {
                    $middleware($request);
                }
                call_user_func_array(self::$route['handler'], $params);
                return true;
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
            foreach (self::$middlewares as $middleware) {
                $middleware($request);
            }
            $newController->$method(...$params);
            return true;
        }

        self::$error = self::NOT_FOUND;
        return false;
    }

}
