<?php
require_once __DIR__ . '/../app/Config/functions.php';

use PHPUnit\Framework\TestCase;
use App\Config\Request\Request;
use App\Config\Request\RequestFacade;
use App\Config\Response\Response;
use App\Config\Response\ResponseFacade;
use App\Config\Response\HttpStatus;

class RequestTest extends TestCase
{
    public function testQueryParameters()
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

    public function testJsonBody()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $json = '{"name":"john"}';

        $request = new Request($json);
        $this->assertSame(['name' => 'john'], $request->body());
    }

    public function testFileUpload()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/upload';
        $_FILES = [
            'file' => ['name' => 'test.txt']
        ];

        $request = new Request();
        $this->assertSame('test.txt', $request->files()['file']['name']);
    }

    public function testStaticRequestMagicCall(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/static';
        $_GET = [];

        $this->assertSame('GET', RequestFacade::method());
        $this->assertSame('/static', RequestFacade::path());
    }

    public function testResponseJson()
    {
        $response = new Response();
        ob_start();
        $response->json(['a' => 1], HttpStatus::CREATED);
        $output = ob_get_clean();

        $this->assertSame('{"a":1}', $output);
        $this->assertSame(HttpStatus::CREATED->value, http_response_code());
    }

    public function testStaticResponseMagicCall(): void
    {
        ob_start();
        ResponseFacade::json(['b' => 2], HttpStatus::CREATED);
        $output = ob_get_clean();

        $this->assertSame('{"b":2}', $output);
        $this->assertSame(HttpStatus::CREATED->value, http_response_code());
    }
}
