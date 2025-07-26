<?php
declare(strict_types=1);

namespace App\Config\Router;

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

    public const BAD_REQUEST = \App\Config\Response\HttpStatus::BAD_REQUEST->value;
    public const NOT_FOUND = \App\Config\Response\HttpStatus::NOT_FOUND->value;
    public const METHOD_NOT_ALLOWED = \App\Config\Response\HttpStatus::METHOD_NOT_ALLOWED->value;
    public const NOT_IMPLEMENTED = \App\Config\Response\HttpStatus::NOT_IMPLEMENTED->value;

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
                call_user_func_array(self::$route['handler'], $params);
                return true;
            }

            $controller = self::$route['handler'];
            $method = self::$route['action'];

            if (class_exists($controller)) {
                $newController = new $controller();
                if (method_exists($controller, $method)) {
                    $params = make([$newController, $method], self::$route['data'] ?? []);
                    $newController->$method(...$params);
                    return true;
                }

                self::$error = self::METHOD_NOT_ALLOWED;
                return false;
            }

            self::$error = self::BAD_REQUEST;
            return false;
        }

        self::$error = self::NOT_FOUND;
        return false;
    }

}
