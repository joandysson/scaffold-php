<?php
declare(strict_types=1);

namespace App\Config\Response;

use App\Config\Response\HttpStatus;
use RuntimeException;
use BadMethodCallException;

class Response
{
    private int $status = HttpStatus::OK->value;
    private array $headers = [];

    public function setStatus(int|HttpStatus $status): self
    {
        $code = $status instanceof HttpStatus ? $status->value : $status;
        $this->status = $code;
        http_response_code($code);
        return $this;
    }

    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        header("$name: $value", true);
        return $this;
    }

    public function json(array $data, int|HttpStatus $status = HttpStatus::OK): void
    {
        $this->setStatus($status);
        $this->addHeader('Content-Type', 'application/json');
        echo json_encode($data);
    }

    public function view(
        string $view,
        array $data = [],
        int|HttpStatus $status = HttpStatus::OK
    ): void {
        $this->setStatus($status);

        $fileDir = 'public' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;

        if (str_contains($view, '..')) {
            throw new RuntimeException('invalid view path');
        }

        $filePath = $fileDir . $view . '.php';
        $baseDir = realpath($fileDir) ?: $fileDir;
        $realPath = realpath($filePath);

        if ($realPath !== false && !str_starts_with($realPath, $baseDir)) {
            throw new RuntimeException('invalid view path');
        }

        if (!is_file($filePath)) {
            throw new RuntimeException('view not found');
        }

        extract($data);

        ob_start();
        include $realPath ?: $filePath;

        echo ob_get_clean();
    }

    public function send(string $content, int|HttpStatus $status = HttpStatus::OK): void
    {
        $this->setStatus($status);
        echo $content;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
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
