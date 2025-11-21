<?php
require_once __DIR__ . '/../config/functions.php';

use PHPUnit\Framework\TestCase;
use Config\Container\Container;
use Config\Request\Request;

class Service {}
class Dependency {}
class ServiceWithDependency
{
    public Dependency $dep;
    public function __construct(Dependency $dep)
    {
        $this->dep = $dep;
    }
}

interface SampleInterface {}
class SampleImplementation implements SampleInterface {}

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

    public function testAutoResolveDependencies(): void
    {
        $object = Container::get(ServiceWithDependency::class);
        $this->assertInstanceOf(Dependency::class, $object->dep);
    }

    public function testResolveBoundInterface(): void
    {
        Container::set(SampleInterface::class, SampleImplementation::class);
        $params = make(function (SampleInterface $s) {}, []);
        $this->assertInstanceOf(SampleImplementation::class, $params[0]);
    }
}
