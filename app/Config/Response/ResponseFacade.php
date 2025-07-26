<?php
declare(strict_types=1);

namespace App\Config\Response;

use BadMethodCallException;

/**
 * Static proxy to {@see Response} allowing fluent response building.
 *
 * @method static self setStatus(int|HttpStatus $status)
 * @method static self addHeader(string $name, string $value)
 * @method static void json(array $data, int|HttpStatus $status = HttpStatus::OK)
 * @method static void view(string $view, array $data = [], int|HttpStatus $status = HttpStatus::OK)
 * @method static void send(string $content, int|HttpStatus $status = HttpStatus::OK)
 * @method static int getStatus()
 * @method static array getHeaders()
 */
class ResponseFacade
{
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $instance = new Response();
        if (!method_exists($instance, $name)) {
            throw new BadMethodCallException("Method {$name} not found in " . Response::class);
        }
        return $instance->$name(...$arguments);
    }
}
