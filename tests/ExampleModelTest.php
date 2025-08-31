<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Model\Example;

class ExampleModelTest extends TestCase
{
    public function testAllThrowsWithoutDatabase(): void
    {
        putenv('DB_DRIVER=mysql');
        putenv('DB_HOST=localhost');
        putenv('DB_NAME=test');
        putenv('DB_USER=root');
        putenv('DB_PASSWORD=secret');

        $this->expectException(Throwable::class);
        (new Example())->all();
    }
}

