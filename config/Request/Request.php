<?php
declare(strict_types=1);

namespace Config\Request;

use Config\Validation\Validator;
use BadMethodCallException;

class Request
{
    private string $method;
    private string $path;
    private array $query;
    private array $body;
    private array $files;
    private array $headers;
    private array $routeParams = [];

    public function __construct(string $rawInput = '')
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = explode('?', $_SERVER['REQUEST_URI'])[0] ?? '/';
        $this->headers = function_exists('getallheaders') ? (getallheaders() ?: []) : [];
        $this->query = $_GET;
        $this->files = $_FILES;
        $this->body = $this->parseBody($rawInput);
    }

    private function parseBody(string $rawInput = ''): array
    {
        $input = $rawInput !== '' ? $rawInput : null;

        if (in_array($this->method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $contentType = $this->headers['Content-Type']
                ?? $this->headers['content-type']
                ?? ($_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? ''));

            if (str_contains($contentType, 'application/json')) {
                $raw = $input ?? file_get_contents('php://input');
                $data = json_decode($raw, true);
                return is_array($data) ? $data : [];
            }

            if ($this->method === 'POST') {
                return $_POST;
            }

            $raw = $input ?? file_get_contents('php://input');
            if ($raw !== '') {
                parse_str($raw, $data);
                return $data;
            }
        }

        return [];
    }

    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(): array
    {
        return $this->query;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function validate(array $rules): array
    {
        return Validator::validate($this->body, $rules);
    }

    public static function __callStatic(string $name, array $arguments): mixed
    {
        $instance = new self();
        if (!method_exists($instance, $name)) {
            throw new BadMethodCallException("Method {$name} not found in " . self::class);
        }
        return $instance->$name(...$arguments);
    }
}
