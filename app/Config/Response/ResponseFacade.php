<?php
declare(strict_types=1);

namespace App\Config\Response;

class ResponseFacade
{
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $instance = new Response();
        if (!method_exists($instance, $name)) {
            throw new \BadMethodCallException("Method {$name} not found in " . Response::class);
        }
        return $instance->$name(...$arguments);
    }
}
