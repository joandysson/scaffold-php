<?php

require_once __DIR__ . '/../config/functions.php';

use Config\Router\Dispatch;
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
            'route' => null,
            'error' => null,
            'separator' => ':',
            'dispatchOnRegistration' => false,
            'hasDispatchedCurrentRequest' => false,
            'registeredMethods' => []
        ] as $name => $value) {
            $prop = new ReflectionProperty(Dispatch::class, $name);
            $prop->setAccessible(true);
            $prop->setValue($value);
        }

        $namedRoutes = new ReflectionProperty(Router::class, 'namedRoutes');
        $namedRoutes->setAccessible(true);
        $namedRoutes->setValue([]);

        $routeTemplates = new ReflectionProperty(Router::class, 'routeTemplates');
        $routeTemplates->setAccessible(true);
        $routeTemplates->setValue([]);
    }

    protected function setUp(): void
    {
        $this->resetRoutes();
    }

    protected function tearDown(): void
    {
        $this->resetRoutes();
    }

    private function enableDispatchOnRegistration(): void
    {
        $dispatchOnRegistration = new ReflectionProperty(Dispatch::class, 'dispatchOnRegistration');
        $dispatchOnRegistration->setAccessible(true);
        $dispatchOnRegistration->setValue(true);
    }

    public function testDispatchOnRegistrationExecutesFirstMatchingRouteAndContinuesRegisteringLaterRoutes(): void
    {
        $this->setServer('GET', '/target/42');

        $this->enableDispatchOnRegistration();

        $executed = false;
        $executedAgain = false;
        Router::get('/other', function (): void {
        });

        Router::get('/target/{id}', function (string $id) use (&$executed): void {
            $executed = $id === '42';
        });
        Router::get('/after-target', function () use (&$executedAgain): void {
            $executedAgain = true;
        }, 'after.target');

        $this->assertTrue($executed);
        $this->assertFalse($executedAgain);
        $this->assertSame('/after-target', Router::route('after.target'));
    }

    public function testDispatchOnRegistrationExecutesFirstMatchingRoute(): void
    {
        $this->setServer('GET', '/posts/123');

        $handledBy = null;

        $this->enableDispatchOnRegistration();

        Router::get('/posts/{id}', function () use (&$handledBy): void {
            $handledBy = 'parameter';
        });

        Router::get('/posts/123', function () use (&$handledBy): void {
            $handledBy = 'exact';
        });

        $this->assertSame('parameter', $handledBy);
    }

    public function testDispatchOnRegistrationKeepsRootRouteWorking(): void
    {
        $this->setServer('GET', '/');

        $this->enableDispatchOnRegistration();

        $executed = false;

        Router::get('/', function () use (&$executed): void {
            $executed = true;
        });

        $this->assertTrue($executed);
        $this->assertSame('/', Router::route('/'));
    }

    public function testDispatchOnRegistrationSupportsGroupedPrefixAndMiddleware(): void
    {
        $this->setServer('GET', '/api/v1/status');

        $this->enableDispatchOnRegistration();

        $middlewareCalled = false;
        $executed = false;

        Router::middleware([
            function (Request $request) use (&$middlewareCalled): void {
                $middlewareCalled = $request->path() === '/api/v1/status';
            }
        ])->group('/api/v1', function (Router $router) use (&$executed): void {
            $router->get('/status', function () use (&$executed): void {
                $executed = true;
            });
        });

        $this->assertTrue($middlewareCalled);
        $this->assertTrue($executed);
    }

    public function testNamedRoutesRemainAvailableWhenDeclaredAfterTheMatchedRoute(): void
    {
        $this->setServer('GET', '/target');

        $this->enableDispatchOnRegistration();

        Router::get('/target', function (): void {
        }, 'current.request');
        Router::get('/contact', function (): void {
        }, 'auth.login');

        $this->assertSame('/contact', Router::route('auth.login'));
    }

    public function testRouteTemplateResolutionRemainsAvailableWithoutNamedRoutes(): void
    {
        $this->setServer('GET', '/target');

        $this->enableDispatchOnRegistration();

        Router::get('/target', function (): void {
        });
        Router::get('/posts/{id}', function (): void {
        });

        $this->assertSame('/posts/42', Router::route('/posts/{id}', ['id' => 42]));
    }

    public function testRouteTemplateResolutionAppendsQueryStringForExtraParameters(): void
    {
        $this->setServer('GET', '/target');

        $this->enableDispatchOnRegistration();

        Router::get('/target', function (): void {
        });
        Router::get('/posts/{id}', function (): void {
        });

        $this->assertSame('/posts/42?tab=comments', Router::route('/posts/{id}', ['id' => 42, 'tab' => 'comments']));
    }

    public function testGroupedRouteTemplateResolutionUsesTheResolvedPrefix(): void
    {
        $this->setServer('GET', '/target');

        $this->enableDispatchOnRegistration();

        Router::get('/target', function (): void {
        });
        Router::group('/api/v2', function (Router $router): void {
            $router->get('/posts/{id}', function (): void {
            });
        });

        $this->assertSame('/api/v2/posts/99', Router::route('/api/v2/posts/{id}', ['id' => 99]));
    }

    public function testRunReturnsTheMatchedRouteResultDuringRegistrationWithoutOverwritingIt(): void
    {
        $this->setServer('GET', '/target');

        $this->enableDispatchOnRegistration();

        $executed = false;

        Router::get('/target', function () use (&$executed): void {
            $executed = true;
        });

        $this->assertTrue(Router::run());
        $this->assertTrue($executed);
        $this->assertNull(Router::error());
    }
}
