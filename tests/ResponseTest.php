<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Config\Response\Response;
use App\Config\Response\HttpStatus;

class ResponseTest extends TestCase
{
    protected function setUp(): void
    {
        http_response_code(200);
        header_remove();
    }

    public function testSetStatusAndAddHeader(): void
    {
        $response = new Response();
        $response->setStatus(HttpStatus::CREATED);
        $response->addHeader('X-Test', '1');
        $this->assertSame(HttpStatus::CREATED->value, $response->getStatus());
        $this->assertSame(['X-Test' => '1'], $response->getHeaders());
    }

    public function testSendOutputsContent(): void
    {
        $response = new Response();
        ob_start();
        $response->send('Hello', HttpStatus::ACCEPTED);
        $output = ob_get_clean();
        $this->assertSame('Hello', $output);
        $this->assertSame(HttpStatus::ACCEPTED->value, http_response_code());
    }

    public function testViewRendersTemplate(): void
    {
        $viewDir = 'public/views/';
        $viewName = 'test-view';
        $viewPath = $viewDir . $viewName . '.php';
        file_put_contents($viewPath, '<?php echo $msg;');

        $response = new Response();
        ob_start();
        $response->view($viewName, ['msg' => 'Hi']);
        $output = ob_get_clean();

        unlink($viewPath);
        $this->assertSame('Hi', $output);
    }

    public function testStaticCallUnknownMethodThrows(): void
    {
        $this->expectException(\BadMethodCallException::class);
        Response::unknown();
    }
}
