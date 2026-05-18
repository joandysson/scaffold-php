<?php

require_once __DIR__ . '/../config/functions.php';

use Config\Router\Dispatch;
use Config\Router\RouteDispatched;
use Config\Router\Router;
use Config\Request\Request;
use PHPUnit\Framework\TestCase;

class RouterDispatchFlowTest extends TestCase
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
            'separator' => ':',
            'dispatchOnRegistration' => false,
            'registeredMethods' => []
        ] as $name => $value) {
            $prop = new ReflectionProperty(Dispatch::class, $name);
            $prop->setAccessible(true);
            $prop->setValue($value);
        }

        $namedRoutes = new ReflectionProperty(Router::class, 'namedRoutes');
        $namedRoutes->setAccessible(true);
        $namedRoutes->setValue([]);
    }

    protected function setUp(): void
    {
        $this->resetRoutes();
    }

    protected function tearDown(): void
    {
        $this->resetRoutes();
    }

    public function testDispatchOnRegistrationExecutesFirstMatchingRouteWithoutReadingRemainingRoutes(): void
    {
        $this->setServer('GET', '/target/42');

        $dispatchOnRegistration = new ReflectionProperty(Dispatch::class, 'dispatchOnRegistration');
        $dispatchOnRegistration->setAccessible(true);
        $dispatchOnRegistration->setValue(true);

        $executed = false;
        Router::get('/other', function (): void {
        });

        try {
            Router::get('/target/{id}', function (string $id) use (&$executed): void {
                $executed = $id === '42';
            });
            Router::get('/after-target', function (): void {
            });
        } catch (RouteDispatched) {
        }

        $routesProperty = new ReflectionProperty(Dispatch::class, 'routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue();

        $this->assertTrue($executed);
        $this->assertSame([], $routes);
    }

    public function testRunExecutesFirstMatchingRoute(): void
    {
        $this->setServer('GET', '/posts/123');

        $handledBy = null;

        Router::get('/posts/{id}', function () use (&$handledBy): void {
            $handledBy = 'parameter';
        });

        Router::get('/posts/123', function () use (&$handledBy): void {
            $handledBy = 'exact';
        });

        Router::run();

        $this->assertSame('parameter', $handledBy);
    }

    public function testDispatchOnRegistrationKeepsRootRouteWorking(): void
    {
        $this->setServer('GET', '/');

        $dispatchOnRegistration = new ReflectionProperty(Dispatch::class, 'dispatchOnRegistration');
        $dispatchOnRegistration->setAccessible(true);
        $dispatchOnRegistration->setValue(true);

        $executed = false;

        try {
            Router::get('/', function () use (&$executed): void {
                $executed = true;
            });
        } catch (RouteDispatched) {
        }

        $this->assertTrue($executed);
    }

    public function testDispatchOnRegistrationSupportsGroupedPrefixAndMiddleware(): void
    {
        $this->setServer('GET', '/api/v1/status');

        $dispatchOnRegistration = new ReflectionProperty(Dispatch::class, 'dispatchOnRegistration');
        $dispatchOnRegistration->setAccessible(true);
        $dispatchOnRegistration->setValue(true);

        $middlewareCalled = false;
        $executed = false;

        try {
            Router::middleware([
                function (Request $request) use (&$middlewareCalled): void {
                    $middlewareCalled = $request->path() === '/api/v1/status';
                }
            ])->group('/api/v1', function (Router $router) use (&$executed): void {
                $router->get('/status', function () use (&$executed): void {
                    $executed = true;
                });
            });
        } catch (RouteDispatched) {
        }

        $this->assertTrue($middlewareCalled);
        $this->assertTrue($executed);
    }

    public function testNamedRoutesRemainAvailableDuringRegistration(): void
    {
        $this->setServer('GET', '/target');

        $dispatchOnRegistration = new ReflectionProperty(Dispatch::class, 'dispatchOnRegistration');
        $dispatchOnRegistration->setAccessible(true);
        $dispatchOnRegistration->setValue(true);

        Router::get('/contact', function (): void {
        }, 'contact.page');

        $this->assertSame('/contact', Router::route('contact.page'));
    }
}
