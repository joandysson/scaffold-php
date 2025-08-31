<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Config\Request\Request;

class CorsMiddleware
{
    public function __invoke(Request $request): void
    {
        $config = require dirname(__DIR__) . '/Config/cors.php';

        if (!($config['enabled'] ?? true)) {
            return;
        }

        $origin = $config['allow_origin'] ?? '*';
        $methods = implode(', ', $config['allow_methods'] ?? []);
        $headers = implode(', ', $config['allow_headers'] ?? []);
        $expose = implode(', ', $config['expose_headers'] ?? []);
        $maxAge = $config['max_age'] ?? 0;
        $credentials = $config['allow_credentials'] ?? false;

        header("Access-Control-Allow-Origin: {$origin}");
        if ($methods !== '') {
            header("Access-Control-Allow-Methods: {$methods}");
        }
        if ($headers !== '') {
            header("Access-Control-Allow-Headers: {$headers}");
        }
        if ($expose !== '') {
            header("Access-Control-Expose-Headers: {$expose}");
        }
        header("Access-Control-Max-Age: {$maxAge}");
        if ($credentials) {
            header('Access-Control-Allow-Credentials: true');
        }

        if ($request->method() === 'OPTIONS') {
            http_response_code(204);
            if (!defined('APP_TESTING')) {
                exit;
            }
            return;
        }
    }
}
