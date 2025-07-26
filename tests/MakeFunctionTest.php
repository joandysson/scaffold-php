<?php
require_once __DIR__ . '/../app/Config/functions.php';

use PHPUnit\Framework\TestCase;
use App\Config\Request\Request;

class Dummy {}

class MakeFunctionTest extends TestCase
{
    public function testMakeInjectsRequest(): void
    {
        $callable = function (Request $r) {
            // no-op
        };
        $params = make($callable, ['id' => 5]);
        $this->assertCount(1, $params);
        $this->assertInstanceOf(Request::class, $params[0]);
        $this->assertSame(['id' => 5], $params[0]->getRouteParams());
    }

    public function testMakeInjectsGenericClass(): void
    {
        $callable = function (Dummy $d, Request $r) {};

        $params = make($callable);
        $this->assertCount(2, $params);
        $this->assertInstanceOf(Dummy::class, $params[0]);
        $this->assertInstanceOf(Request::class, $params[1]);
    }

    public function testMakeInjectsRouteParameters(): void
    {
        $callable = function (int $id, string $slug) {};
        $params = make($callable, ['id' => 1, 'slug' => 'a']);
        $this->assertSame([1, 'a'], $params);
    }
}
