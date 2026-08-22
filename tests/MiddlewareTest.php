<?php
namespace MiddlewareTestNamespace {
    use Config\Request\Request;
    class FlagMiddleware
    {
        public static bool $called = false;
        public function __invoke(Request $request): void
        {
            self::$called = true;
        }
    }
}

namespace {
    require_once __DIR__ . '/../config/functions.php';
    use PHPUnit\Framework\TestCase;
    use Config\Router\Router;
    use Config\Router\Dispatch;
    use Config\Router\RouteDispatched;
    use MiddlewareTestNamespace\FlagMiddleware;

    class MiddlewareTest extends TestCase
    {
        private function setDispatchProperty(string $name, mixed $value): void
        {
            $property = new \ReflectionProperty(Dispatch::class, $name);
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
                'middlewares' => [],
                'dispatchOnRegistration' => false,
                'registeredMethods' => []
            ] as $name => $value) {
                $prop = new \ReflectionProperty(Dispatch::class, $name);
                $prop->setAccessible(true);
                $prop->setValue($value);
            }
        }

        private function dispatchRoutes(callable $callback): void
        {
            $dispatchOnRegistration = new \ReflectionProperty(Dispatch::class, 'dispatchOnRegistration');
            $dispatchOnRegistration->setAccessible(true);
            $dispatchOnRegistration->setValue(true);

            try {
                $callback();
            } catch (RouteDispatched) {
            } finally {
                $dispatchOnRegistration->setValue(false);
            }
        }

        protected function setUp(): void
        {
            $this->resetRoutes();
            FlagMiddleware::$called = false;
        }

        public function testMiddlewareIsExecuted(): void
        {
            $this->setServer('GET', '/middleware');
            Router::addMiddleware(new FlagMiddleware());
            ob_start();
            $this->dispatchRoutes(function (): void {
                Router::get('/middleware', function () {
                    echo 'ok';
                });
            });
            ob_get_clean();
            $this->assertTrue(FlagMiddleware::$called);
        }

        public function testMiddlewareInstantiatedFromClassName(): void
        {
            $this->setServer('GET', '/middleware-class');
            Router::addMiddleware(FlagMiddleware::class);
            ob_start();
            $this->dispatchRoutes(function (): void {
                Router::get('/middleware-class', function () {
                    echo 'ok';
                });
            });
            ob_get_clean();
            $this->assertTrue(FlagMiddleware::$called);
        }

        public function testRouteSpecificMiddlewareIsExecuted(): void
        {
            $this->setServer('GET', '/middleware-scoped');
            ob_start();
            $this->dispatchRoutes(function (): void {
                Router::middleware([FlagMiddleware::class])->get('/middleware-scoped', function () {
                    echo 'ok';
                });
            });
            ob_get_clean();
            $this->assertTrue(FlagMiddleware::$called);
        }

        public function testRouteSpecificMiddlewareDoesNotAffectOtherRoutes(): void
        {
            $this->setServer('GET', '/no-middleware');
            ob_start();
            $this->dispatchRoutes(function (): void {
                Router::middleware([FlagMiddleware::class])->get('/middleware-scoped', function () {
                    echo 'ok';
                });
                Router::get('/no-middleware', function () {
                    echo 'ok';
                });
            });
            ob_get_clean();
            $this->assertFalse(FlagMiddleware::$called);
        }
    }
}
