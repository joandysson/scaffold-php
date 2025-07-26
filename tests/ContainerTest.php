<?php
require_once __DIR__ . '/../app/Config/functions.php';

use PHPUnit\Framework\TestCase;
use App\Config\Container\Container;
use App\Config\Request\Request;

class Service {}

class ContainerTest extends TestCase
{
    protected function tearDown(): void
    {
        Container::clear();
    }

    public function testResolveRegisteredService(): void
    {
        $instance = new Service();
        Container::set(Service::class, $instance);
        $params = make(function (Service $s) {}, []);
        $this->assertSame($instance, $params[0]);
    }

    public function testRequestInjectedWithRouteParams(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/foo';
        $params = make(function (Request $r) {}, ['id' => 10]);
        $this->assertSame(['id' => 10], $params[0]->getRouteParams());
    }
}
