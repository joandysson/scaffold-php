<?php
namespace {
    use PHPUnit\Framework\TestCase;
    use App\Middleware\CorsMiddleware;

    class MiddlewareConfigTest extends TestCase
    {
        public function testCorsMiddlewareIncludedByDefault(): void
        {
            $middlewares = require __DIR__ . '/../app/Config/middleware.php';
            $this->assertContains(CorsMiddleware::class, $middlewares);
        }
    }
}
