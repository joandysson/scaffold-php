<?php
namespace MiddlewareTestNamespace {
    use App\Config\Request\Request;
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
    require_once __DIR__ . '/../app/Config/functions.php';
    use PHPUnit\Framework\TestCase;
    use App\Config\Router\Router;
    use App\Config\Router\Dispatch;
    use MiddlewareTestNamespace\FlagMiddleware;

    class MiddlewareTest extends TestCase
    {
        private function setServer(string $method, string $uri): void
        {
            $_SERVER['REQUEST_METHOD'] = $method;
            $_SERVER['REQUEST_URI'] = $uri;
            $patchProp = new \ReflectionProperty(Dispatch::class, 'patch');
            $patchProp->setAccessible(true);
            $patchProp->setValue(explode('?', $uri)[0]);

            $methodProp = new \ReflectionProperty(Dispatch::class, 'httpMethod');
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
                'middlewares' => []
            ] as $name => $value) {
                $prop = new \ReflectionProperty(Dispatch::class, $name);
                $prop->setAccessible(true);
                $prop->setValue($value);
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
            Router::get('/middleware', function () {
                echo 'ok';
            });
            ob_start();
            Router::run();
            ob_get_clean();
            $this->assertTrue(FlagMiddleware::$called);
        }

        public function testMiddlewareInstantiatedFromClassName(): void
        {
            $this->setServer('GET', '/middleware-class');
            Router::addMiddleware(FlagMiddleware::class);
            Router::get('/middleware-class', function () {
                echo 'ok';
            });
            ob_start();
            Router::run();
            ob_get_clean();
            $this->assertTrue(FlagMiddleware::$called);
        }
    }
}
