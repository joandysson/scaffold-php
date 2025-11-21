<?php

require_once __DIR__ . '/../app/Config/functions.php';

use App\Config\Request\Request;
use App\Config\Response\Response;
use App\Config\Router\Dispatch;
use App\Config\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterMiddlewareGroupTest extends TestCase
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

    public function testMiddlewareGroupRegistersRoutes(): void
    {
        $this->setServer('GET', '/grouped');

        $middlewareCalled = false;

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

        Router::run();

        $this->assertTrue($middlewareCalled);
    }

    public function testMiddlewareGroupWithPrefixRegistersRoutes(): void
    {
        $this->setServer('POST', '/api/v2/grouped');

        $middlewareCalled = false;

        Router::middleware([
            function (Request $request) use (&$middlewareCalled): void {
                $middlewareCalled = true;
            }
        ])->group('/api/v2', function (Router $router): void {
            $router->post('/grouped', function () {
                return 'ok';
            });
        });

        Router::get('/outside', function () {
            return 'outside';
        });

        Router::run();

        $routesProperty = new ReflectionProperty(Dispatch::class, 'routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertTrue($middlewareCalled);
        $this->assertArrayHasKey('/outside', $routes['GET']);
    }

    public function testRouterGroupPrefixesRoutes(): void
    {
        $this->setServer('GET', '/api/v3/users');

        Router::group('/api/v3', function (Router $router): void {
            $router->get('/users', function () {
                return 'users';
            });
        });

        Router::get('/no-prefix', function () {
            return 'noprefix';
        });

        Router::run();

        $routesProperty = new ReflectionProperty(Dispatch::class, 'routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertArrayHasKey('/no-prefix', $routes['GET']);
        $this->assertArrayHasKey('/api/v3/users', $routes['GET']);
    }
}
