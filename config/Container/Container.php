<?php
declare(strict_types=1);

namespace App\Config\Container;

use Closure;
use ReflectionClass;
use ReflectionNamedType;

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
        $target = $id;

        if (isset(self::$bindings[$id])) {
            $concrete = self::$bindings[$id];
            if ($concrete instanceof Closure) {
                return $concrete();
            }

            if (is_object($concrete)) {
                return $concrete;
            }

            $target = is_string($concrete) ? $concrete : $id;
        }

        return self::build($target);
    }

    private static function build(string $class): object
    {
        $ref = new ReflectionClass($class);
        $constructor = $ref->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $params = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $params[] = self::get($type->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } else {
                $params[] = null;
            }
        }

        return $ref->newInstanceArgs($params);
    }

    public static function clear(): void
    {
        self::$bindings = [];
    }
}
