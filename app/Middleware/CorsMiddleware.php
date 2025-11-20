<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Config\Request\Request;
use App\Config\Response\HttpStatus;
use App\Config\Response\Response;

class CorsMiddleware
{
    public function __invoke(Request $request): void
    {
        $config = require dirname(__DIR__) . '/Config/cors.php';

        if (!($config['enabled'] ?? true)) {
            return;
        }

        $response = new Response();

        $origin = $config['allow_origin'] ?? '*';
        $methods = implode(', ', $config['allow_methods'] ?? []);
        $headers = implode(', ', $config['allow_headers'] ?? []);
        $expose = implode(', ', $config['expose_headers'] ?? []);
        $maxAge = $config['max_age'] ?? 0;
        $credentials = $config['allow_credentials'] ?? false;

        $response->addHeader('Access-Control-Allow-Origin', (string) $origin);
        if ($methods !== '') {
            $response->addHeader('Access-Control-Allow-Methods', $methods);
        }
        if ($headers !== '') {
            $response->addHeader('Access-Control-Allow-Headers', $headers);
        }
        if ($expose !== '') {
            $response->addHeader('Access-Control-Expose-Headers', $expose);
        }
        $response->addHeader('Access-Control-Max-Age', (string) $maxAge);
        if ($credentials) {
            $response->addHeader('Access-Control-Allow-Credentials', 'true');
        }

        if ($request->method() === 'OPTIONS') {
            $response->send('', HttpStatus::NO_CONTENT);
            return;
        }
    }
}
