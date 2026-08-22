<?php
require_once __DIR__ . '/../config/functions.php';

use PHPUnit\Framework\TestCase;
use Config\Router\Router;
use Config\Router\Dispatch;

class RouterExceptionTest extends TestCase
{
    private function setDispatchProperty(string $name, mixed $value): void
    {
        $property = new ReflectionProperty(Dispatch::class, $name);
        $property->setValue(null, $value);
    }

    private function setServer(string $method, string $uri): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $this->setDispatchProperty('patch', explode('?', $uri)[0]);
        $this->setDispatchProperty('httpMethod', $method);
    }

    private function resetRoutes(): void
    {
        foreach ([
            'routes' => [],
            'route' => null,
            'error' => null,
            'separator' => ':'
        ] as $name => $value) {
            $this->setDispatchProperty($name, $value);
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
