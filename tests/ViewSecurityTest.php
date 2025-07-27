<?php
require_once __DIR__ . '/../app/Config/functions.php';

use PHPUnit\Framework\TestCase;
use App\Config\Response\Response;

class ViewSecurityTest extends TestCase
{
    public function testEmailViewRejectsTraversal(): void
    {
        $this->expectException(RuntimeException::class);
        emailView('../views/home');
    }

    public function testResponseViewRejectsTraversal(): void
    {
        $response = new Response();
        $this->expectException(RuntimeException::class);
        $response->view('../views/home');
    }
}

