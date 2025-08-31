<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Middleware\CorsMiddleware;
use App\Config\Request\Request;

class CorsMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        http_response_code(200);
        header_remove();
        if (!defined('APP_TESTING')) {
            define('APP_TESTING', true);
        }
    }

    public function testSetsCorsHeaders(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->any())
            ->method('method')
            ->willReturn('GET');

        $middleware = new CorsMiddleware();
        $middleware($request);

        $this->assertSame(200, http_response_code());
    }

    public function testOptionsRequestReturnsNoContent(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->any())
            ->method('method')
            ->willReturn('OPTIONS');

        $middleware = new CorsMiddleware();
        ob_start();
        $middleware($request);
        ob_end_clean();

        $this->assertSame(204, http_response_code());
    }
}

