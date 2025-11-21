<?php
require_once __DIR__ . '/../config/functions.php';

use PHPUnit\Framework\TestCase;
use App\Config\Router\Router;
use App\Config\Router\Dispatch;

class RouterExceptionTest extends TestCase
{
    private function setServer(string $method, string $uri): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $patchProp = new ReflectionProperty(Dispatch::class, 'patch');
        $patchProp->setAccessible(true);
        $patchProp->setValue(explode('?', $uri)[0]);

        $methodProp = new ReflectionProperty(Dispatch::class, 'httpMethod');
        $methodProp->setAccessible(true);
        $methodProp->setValue($method);
    }

    private function resetRoutes(): void
    {
        foreach ([
            'routes' => [],
            'route' => null,
            'error' => null,
            'separator' => ':'
        ] as $name => $value) {
            $prop = new ReflectionProperty(Dispatch::class, $name);
            $prop->setAccessible(true);
            $prop->setValue($value);
        }
    }

    protected function setUp(): void
    {
        $this->resetRoutes();
    }

    public function testExceptionForMissingControllerMethod(): void
    {
        $this->setServer('GET', '/foo');
        Router::get('/foo', 'HomeController:missingMethod');
        $this->expectException(RuntimeException::class);
        Router::run();
    }
}
