<?php
namespace App\Controller {
    use Config\Request\Request;

    class DummyInjectionController
    {
        public static ?Request $captured = null;
        public function handle(Request $request): void
        {
            self::$captured = $request;
        }
    }
}

namespace {
    require_once __DIR__ . '/../config/functions.php';
    use PHPUnit\Framework\TestCase;
    use Config\Router\Router;
    use Config\Router\Dispatch;
    use Config\Request\Request;
    use App\Controller\DummyInjectionController;

    class RouterInjectionTest extends TestCase
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
                $prop = new ReflectionProperty(Dispatch::class, $name);
                $prop->setAccessible(true);
                $prop->setValue($value);
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
            } finally {
                $dispatchOnRegistration->setValue(false);
            }
        }

        public function testClosureInjection(): void
        {
            $this->setServer('GET', '/inject');
            $captured = null;
            $this->dispatchRoutes(function () use (&$captured): void {
                Router::get('/inject', function (Request $req) use (&$captured) {
                    $captured = $req;
                });
            });
            $this->assertInstanceOf(Request::class, $captured);
        }

        public function testControllerMethodInjection(): void
        {
            $this->setServer('GET', '/controller');
            $this->dispatchRoutes(function (): void {
                Router::get('/controller', 'DummyInjectionController:handle');
            });
            $this->assertInstanceOf(Request::class, DummyInjectionController::$captured);
        }

        public function testRouteParametersAreInjectedIntoRequest(): void
        {
            $this->setServer('GET', '/blog/42/test-slug');
            $captured = null;
            $this->dispatchRoutes(function () use (&$captured): void {
                Router::get('/blog/{id}/{slug}', function (Request $req) use (&$captured) {
                    $captured = $req->getRouteParams();
                });
            });
            $this->assertSame(['id' => '42', 'slug' => 'test-slug'], $captured);
        }
    }
}
