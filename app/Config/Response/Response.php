<?php

namespace App\Config\Response;

use App\Config\Response\HttpStatus;

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

    public function send(string $content, int|HttpStatus $status = HttpStatus::OK): void
    {
        $this->setStatus($status);
        echo $content;
    }
}
