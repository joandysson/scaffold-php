<?php
declare(strict_types=1);

namespace App\Config\Response;

use App\Config\Response\HttpStatus;
use BadMethodCallException;
use RuntimeException;

/**
 * @method Response setStatus(int|HttpStatus $status)
 * @method Response addHeader(string $name, string $value)
 * @method Response json(array $data, int|HttpStatus $status = HttpStatus::OK)
 * @method Response view(string $view, array $data = [], int|HttpStatus $status = HttpStatus::OK)
 * @method void send(?string $content = null, int|HttpStatus $status = HttpStatus::OK)
 * @method int getStatus()
 * @method array getHeaders()
 * @method static Response setStatus(int|HttpStatus $status)
 * @method static Response addHeader(string $name, string $value)
 * @method static Response json(array $data, int|HttpStatus $status = HttpStatus::OK)
 * @method static Response view(string $view, array $data = [], int|HttpStatus $status = HttpStatus::OK)
 * @method static void send(?string $content = null, int|HttpStatus $status = HttpStatus::OK)
 * @method static int getStatus()
 * @method static array getHeaders()
 */
class Response
{
    private int $status = HttpStatus::OK->value;
    private array $headers = [];
    private string $content = '';

    public function __call(string $name, array $arguments): mixed
    {
        if (!method_exists($this, $name)) {
            throw new BadMethodCallException("Method {$name} not found in " . self::class);
        }

        return $this->$name(...$arguments);
    }

    protected function setStatus(int|HttpStatus $status): self
    {
        $code = $status instanceof HttpStatus ? $status->value : $status;
        $this->status = $code;
        http_response_code($code);
        return $this;
    }

    protected function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        header("$name: $value", true);
        return $this;
    }

    protected function json(array $data, int|HttpStatus $status = HttpStatus::OK): self
    {
        $this->setStatus($status);
        $this->addHeader('Content-Type', 'application/json');
        $this->content = json_encode($data);
        return $this;
    }

    protected function view(
        string $view,
        array $data = [],
        int|HttpStatus $status = HttpStatus::OK
    ): self {
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

        $this->content = ob_get_clean();
        return $this;
    }

    protected function send(?string $content = null, int|HttpStatus $status = HttpStatus::OK): void
    {
        if ($content !== null) {
            $this->content = $content;
        }
        if ($status !== null) {
            $this->setStatus($status);
        }
        echo $this->content;
    }

    protected function getStatus(): int
    {
        return $this->status;
    }

    protected function getHeaders(): array
    {
        return $this->headers;
    }

    public static function __callStatic(string $name, array $arguments): mixed
    {
        $instance = new static();
        if (!method_exists($instance, $name)) {
            throw new BadMethodCallException("Method {$name} not found in " . self::class);
        }
        return $instance->$name(...$arguments);
    }
}
