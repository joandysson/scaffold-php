<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Controller\HomeController;
use App\Config\Request\Request;

class HomeControllerTest extends TestCase
{
    private function requestWith(array $params = []): Request
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->any())
            ->method('getRouteParams')
            ->willReturn($params);
        return $request;
    }

    public function testHomeOutputsMessage(): void
    {
        $controller = new HomeController();
        ob_start();
        $controller->home($this->requestWith());
        $output = ob_get_clean();
        $this->assertSame("Welcome to the Home Page!\n", $output);
    }

    public function testBlogOutputsIdAndSlug(): void
    {
        $controller = new HomeController();
        $request = $this->requestWith(['id' => '1', 'slug' => 'post']);
        ob_start();
        $controller->blog($request);
        $output = ob_get_clean();
        $this->assertSame("Blog post 1 - post\n", $output);
    }

    public function testUpdateOutputsId(): void
    {
        $controller = new HomeController();
        $request = $this->requestWith(['id' => '2']);
        ob_start();
        $controller->update($request);
        $output = ob_get_clean();
        $this->assertSame("Updated post 2\n", $output);
    }

    public function testPartialUpdateOutputsId(): void
    {
        $controller = new HomeController();
        $request = $this->requestWith(['id' => '3']);
        ob_start();
        $controller->partialUpdate($request);
        $output = ob_get_clean();
        $this->assertSame("Partially updated post 3\n", $output);
    }

    public function testDeleteOutputsId(): void
    {
        $controller = new HomeController();
        $request = $this->requestWith(['id' => '4']);
        ob_start();
        $controller->delete($request);
        $output = ob_get_clean();
        $this->assertSame("Deleted post 4\n", $output);
    }

    public function testContactOutputsMessage(): void
    {
        $controller = new HomeController();
        ob_start();
        $controller->contact();
        $output = ob_get_clean();
        $this->assertSame("Contact page\n", $output);
    }
}

