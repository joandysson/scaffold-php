<?php

namespace App\Config\Response;

class Response
{
    private int $status = 200;
    private array $headers = [];

    public function setStatus(int $status): self
    {
        $this->status = $status;
        http_response_code($status);
        return $this;
    }

    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        header("$name: $value", true);
        return $this;
    }

    public function json(array $data, int $status = 200): void
    {
        $this->setStatus($status);
        $this->addHeader('Content-Type', 'application/json');
        echo json_encode($data);
    }

    public function send(string $content, int $status = 200): void
    {
        $this->setStatus($status);
        echo $content;
    }
}
