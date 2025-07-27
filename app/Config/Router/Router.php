<?php
declare(strict_types=1);

namespace App\Config\Router;

use Closure;
use App\Config\Response\HttpStatus;

class Router extends Dispatch
{
    /**
     * Base namespace for controller. Defaults to 'App\Controller'.
     * Can be changed using setNamespace().
     */
    private static string $namespace = 'App\Controller';

    private static string $prefix = '';

    public function __construct()
    {
        parent::__construct();
        self::init();
    }

    public static function init(): void
    {
        require_once dirname(__DIR__) . '../../../routes/web.php';
    }

    public static function prefix(string $prefix = ''): void
    {
        self::$prefix = $prefix;
    }

    /**
     * Change the namespace used to resolve controller classes.
     */
    public static function setNamespace(string $namespace): void
    {
        self::$namespace = trim($namespace, '\\');
    }

    /**
     * Get the current controller namespace.
     */
    public static function getNamespace(): string
    {
        return self::$namespace;
    }

    public static function post(string $route, callable|string $handler, ?string $name = null): void
    {
        self::addRoute('POST', $route, $handler, $name);
    }

    public static function get(string $route, callable|string $handler, ?string $name = null): void
    {
        self::addRoute('GET', $route, $handler, $name);
    }

    public static function put(string $route, callable|string $handler, ?string $name = null): void
    {
        self::addRoute('PUT', $route, $handler, $name);
    }

    public static function patch(string $route, callable|string $handler, ?string $name = null): void
    {
        self::addRoute('PATCH', $route, $handler, $name);
    }

    public static function delete(string $route, callable|string $handler, ?string $name = null): void
    {
        self::addRoute('DELETE', $route, $handler, $name);
    }

    protected static function addRoute(
        string $method,
        string $route,
        callable|string $handler,
        ?string $name = null
    ): void
    {
        if (self::$prefix !== '') {
            $route = self::$prefix . ($route === '/' ? '' : $route);
        }

        if ($route == '/') {
            self::addRoute($method, '', $handler, $name);
        }

        preg_match_all('~\{\s* ([a-zA-Z_][a-zA-Z0-9_-]*) }~x', $route, $keys, PREG_SET_ORDER);
        $routeDiff = array_values(array_diff(explode('/', parent::$patch), explode('/', $route)));

        $offset = parent::$group ? 1 : 0;
        $params = [];
        foreach ($keys as $key) {
            $params[$key[1]] = $routeDiff[$offset] ?? null;
            $offset++;
        }

        $route = (!parent::$group ? $route : '/' . parent::$group . "{$route}");

        $data = $params;

        $namespace = self::$namespace;
        $router = function () use ($method, $handler, $data, $route, $name, $namespace) {
            return [
                'route' => $route,
                'name' => $name,
                'method' => $method,
                'handler' => self::handler($handler, $namespace),
                'action' => self::action($handler),
                'data' => $data
            ];
        };

        $route = preg_replace('~{([^}]*)}~', '([^/]+)', $route);

        parent::$routes[$method][$route] = $router();
    }

    private static function handler(callable|string $handler, string $namespace): Closure|string
    {
        return (!is_string($handler) ? $handler : "{$namespace}\\" . explode(parent::$separator, $handler)[0]);
    }

    private static function action(callable|string $handler): bool|string|null
    {
        return (!is_string($handler)
            ?: (explode(parent::$separator, $handler)[1] ? explode(parent::$separator, $handler)[1] : null));
    }

    public static function route(string $name, ?array $data = null): ?string
    {
        foreach (static::$routes as $http_verb) {
            foreach ($http_verb as $route_item) {
                if (!empty($route_item['name']) && $route_item['name'] == $name || $route_item['route'] === $name) {
                    $route_item['route'] = empty($route_item['route']) ? '/' : $route_item['route'];

                    return self::treat($route_item, $data);
                }
            }
        }

        return null;
    }

    public static function redirect(
        string $route,
        ?array $data = null,
        int|HttpStatus $status = HttpStatus::MOVED_PERMANENTLY
    ): void
    {
        $code = $status instanceof HttpStatus ? $status->value : $status;
        http_response_code($code);

        if ($name = self::route($route, $data)) {
            header("Location: {$name}");
            exit;
        }

        if (filter_var($route, FILTER_VALIDATE_URL)) {

            header("Location: {$route}");
            exit;
        }

        $route = (str_starts_with($route, '/') ? $route : "/{$route}");
        header("Location: " . parent::$projectUrl . "{$route}");
        exit;
    }

    public static function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        $host = $_SERVER['HTTP_HOST'] ?? parse_url(parent::$projectUrl ?? getenv('APP_URL') ?: '', PHP_URL_HOST);

        if ($referer && filter_var($referer, FILTER_VALIDATE_URL)) {
            $refererHost = parse_url($referer, PHP_URL_HOST);

            if ($refererHost === $host) {
                header("Location: {$referer}");
                exit;
            }
        }

        header('Location: /');
        exit;
    }

    private static function treat(array $route_item, ?array $data = null): string
    {
        $route = $route_item['route'];

        if (!empty($data)) {
            $arguments = [];
            $params = [];
            foreach ($data as $key => $value) {
                if (!str_contains($route, "{{$key}}")) {
                    $params[$key] = $value;
                }
                $arguments["{{$key}}"] = $value;
            }
            $route = self::process($route, $arguments, $params);
        }

        return parent::$projectUrl . $route;
    }

    private static function process(string $route, array $arguments, ?array $params = null): string
    {
        $params = (!empty($params) ? '?' . http_build_query($params) : null);
        return str_replace(array_keys($arguments), array_values($arguments), $route) . "{$params}";
    }

    public static function getUrl(?int $slice = null): array|string
    {
        $url = $_SERVER['REDIRECT_URL'] ?? ($_SERVER['REQUEST_URI'] ?? '/');
        $route = explode('/', $url);

        if ($slice !== null) {
            return $route[$slice] ?? '';
        }

        return $route;
    }
}
