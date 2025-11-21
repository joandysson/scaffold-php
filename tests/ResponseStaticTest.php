<?php
require_once __DIR__ . '/../config/functions.php';

use PHPUnit\Framework\TestCase;
use Config\Response\Response;
use Config\Response\HttpStatus;

class ResponseStaticTest extends TestCase
{
    protected function setUp(): void
    {
        http_response_code(HttpStatus::OK->value);
    }

    public function testStaticJsonResponse(): void
    {
        ob_start();
        $response = Response::json(['static' => 'ok'], HttpStatus::ACCEPTED);
        $response->send();
        $output = ob_get_clean();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('{"static":"ok"}', $output);
        $this->assertSame(HttpStatus::ACCEPTED->value, http_response_code());
    }

    public function testStaticSendHandlesStatus(): void
    {
        ob_start();
        Response::send('static send', HttpStatus::CREATED);
        $output = ob_get_clean();

        $this->assertSame('static send', $output);
        $this->assertSame(HttpStatus::CREATED->value, http_response_code());
    }
}
