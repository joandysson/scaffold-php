<?php

require_once __DIR__ . '/../config/functions.php';

use Config\Request\Request;
use Config\Response\Response;
use Config\Router\Dispatch;
use Config\Router\RouteDispatched;
use Config\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterMiddlewareGroupTest extends TestCase
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
            'registeredMethods' => []
        ] as $name => $value) {
            $this->setDispatchProperty($name, $value);
        }
    }

    protected function setUp(): void
    {
        $this->resetRoutes();
    }

    private function dispatchRoutes(callable $callback): void
    {
        $dispatchOnRegistration = new ReflectionProperty(Dispatch::class, 'dispatchOnRegistration');
        $dispatchOnRegistration->setAccessible(true);
        $dispatchOnRegistration->setValue(true);

        try {
            $callback();
        } catch (RouteDispatched) {
        } finally {
            $dispatchOnRegistration->setValue(false);
        }
    }

    public function testMiddlewareGroupRegistersRoutes(): void
    {
        $this->setServer('GET', '/grouped');

        $middlewareCalled = false;

        $this->dispatchRoutes(function () use (&$middlewareCalled): void {
            Router::middleware([
                function (Request $request) use (&$middlewareCalled): Response {
                    $middlewareCalled = true;

                    return new Response();
                }
            ])->group(function (Router $router) {
                $router->get('/grouped', function () {
                    return 'ok';
                });
            });
        });

        $this->assertTrue($middlewareCalled);
    }

    public function testMiddlewareGroupWithPrefixRegistersRoutes(): void
    {
        $this->setServer('POST', '/api/v2/grouped');

        $middlewareCalled = false;

        $this->dispatchRoutes(function () use (&$middlewareCalled): void {
            Router::middleware([
                function (Request $request) use (&$middlewareCalled): void {
                    $middlewareCalled = true;
                }
            ])->group('/api/v2', function (Router $router): void {
                $router->post('/grouped', function () {
                    return 'ok';
                });
            });
        });

        $this->assertTrue($middlewareCalled);
    }

    public function testRouterGroupPrefixesRoutes(): void
    {
        $this->setServer('GET', '/api/v3/users');

        $executed = false;

        $this->dispatchRoutes(function () use (&$executed): void {
            Router::group('/api/v3', function (Router $router) use (&$executed): void {
                $router->get('/users', function () use (&$executed): void {
                    $executed = true;
                });
            });
        });

        $this->assertTrue($executed);
    }
}
