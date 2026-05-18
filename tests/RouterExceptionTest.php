<?php
require_once __DIR__ . '/../config/functions.php';

use PHPUnit\Framework\TestCase;
use Config\Router\Router;
use Config\Router\Dispatch;

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
            'route' => null,
            'error' => null,
            'separator' => ':',
            'dispatchOnRegistration' => false,
            'registeredMethods' => []
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
