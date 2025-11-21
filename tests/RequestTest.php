<?php
require_once __DIR__ . '/../config/functions.php';

use PHPUnit\Framework\TestCase;
use Config\Request\Request;
use Config\Response\Response;
use Config\Response\HttpStatus;

class RequestTest extends TestCase
{
    public function testQueryParameters(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/foo?bar=baz';
        $_GET = ['bar' => 'baz'];

        $request = new Request();
        $this->assertSame('GET', $request->method());
        $this->assertSame('/foo', $request->path());
        $this->assertSame(['bar' => 'baz'], $request->query());
        $this->assertSame('baz', $request->get('bar'));
    }

    public function testJsonBody(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $json = '{"name":"john"}';

        $request = new Request($json);
        $this->assertSame(['name' => 'john'], $request->body());
    }

    public function testFileUpload(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/upload';
        $_FILES = [
            'file' => ['name' => 'test.txt']
        ];

        $request = new Request();
        $this->assertSame('test.txt', $request->files()['file']['name']);
    }

    public function testRequestFacadeBehavior(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/static';
        $_GET = [];

        $request = new Request();
        $this->assertSame('GET', $request->method());
        $this->assertSame('/static', $request->path());
    }

    public function testResponseJson(): void
    {
        $response = new Response();
        ob_start();
        $response->json(['a' => 1], HttpStatus::CREATED)->send();
        $output = ob_get_clean();

        $this->assertSame('{"a":1}', $output);
        $this->assertSame(HttpStatus::CREATED->value, http_response_code());
    }

    public function testResponseFacadeBehavior(): void
    {
        $response = new Response();
        ob_start();
        $response->json(['b' => 2], HttpStatus::CREATED)->send();
        $output = ob_get_clean();

        $this->assertSame('{"b":2}', $output);
        $this->assertSame(HttpStatus::CREATED->value, http_response_code());
    }
}
