<?php
declare(strict_types=1);

namespace App\Config\Container;

use Closure;

class Container
{
    /**
     * @var array<string, callable|object|string>
     */
    private static array $bindings = [];

    public static function set(string $id, callable|object|string $concrete): void
    {
        self::$bindings[$id] = $concrete;
    }

    public static function get(string $id): object
    {
        if (!isset(self::$bindings[$id])) {
            return new $id();
        }

        $concrete = self::$bindings[$id];
        if ($concrete instanceof Closure) {
            return $concrete();
        }

        if (is_string($concrete)) {
            return new $concrete();
        }

        return $concrete;
    }

    public static function clear(): void
    {
        self::$bindings = [];
    }
}
