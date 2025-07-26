<?php
declare(strict_types=1);

namespace App\Config\Request;

use BadMethodCallException;

/**
 * Static proxy to {@see Request} allowing static access to request data.
 *
 * @method static void setRouteParams(array $params)
 * @method static array getRouteParams()
 * @method static string method()
 * @method static string path()
 * @method static array query()
 * @method static array body()
 * @method static array files()
 * @method static array headers()
 * @method static mixed get(string $key, mixed $default = null)
 * @method static array validate(array $rules)
 */
class RequestFacade
{
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $instance = new Request();
        if (!method_exists($instance, $name)) {
            throw new BadMethodCallException("Method {$name} not found in " . Request::class);
        }
        return $instance->$name(...$arguments);
    }
}
