<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Utils\Utils;

class UtilsTest extends TestCase
{
    public function testTraitIsUsable(): void
    {
        $obj = new class {
            use Utils;
        };
        $this->assertContains(Utils::class, class_uses($obj));
    }
}

