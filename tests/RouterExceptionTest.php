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
            'route' => null,
            'error' => null,
            'separator' => ':',
            'dispatchOnRegistration' => false,
            'hasDispatchedCurrentRequest' => false,
            'registeredMethods' => []
        ] as $name => $value) {
            $this->setDispatchProperty($name, $value);
        }
    }

    protected function setUp(): void
    {
        $this->resetRoutes();
    }

    private function enableDispatchOnRegistration(): void
    {
        $dispatchOnRegistration = new ReflectionProperty(Dispatch::class, 'dispatchOnRegistration');
        $dispatchOnRegistration->setAccessible(true);
        $dispatchOnRegistration->setValue(true);
    }

    public function testExceptionForMissingControllerMethod(): void
    {
        $this->setServer('GET', '/foo');
        $this->enableDispatchOnRegistration();
        $this->expectException(RuntimeException::class);
        Router::get('/foo', 'HomeController:missingMethod');
    }
}
