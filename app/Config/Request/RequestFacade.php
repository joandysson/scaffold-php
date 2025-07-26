<?php
declare(strict_types=1);

namespace App\Config\Request;

class RequestFacade
{
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $instance = new Request();
        if (!method_exists($instance, $name)) {
            throw new \BadMethodCallException("Method {$name} not found in " . Request::class);
        }
        return $instance->$name(...$arguments);
    }
}
