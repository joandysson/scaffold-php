<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Config\Request\Request;
use App\Config\Response\HttpStatus;

class SwaggerAuthMiddleware
{
    public function __invoke(Request $request): void
    {
        $username = getenv('SWAGGER_USERNAME') ?: null;
        $password = getenv('SWAGGER_PASSWORD') ?: null;

        if ($username === null || $password === null) {
            http_response_code(HttpStatus::INTERNAL_SERVER_ERROR->value);
            echo 'Swagger credentials are not configured.';
            exit;
        }

        $providedUser = $_SERVER['PHP_AUTH_USER'] ?? null;
        $providedPassword = $_SERVER['PHP_AUTH_PW'] ?? null;

        if ($providedUser !== $username || $providedPassword !== $password) {
            header('WWW-Authenticate: Basic realm="Swagger Documentation"');
            http_response_code(HttpStatus::UNAUTHORIZED->value);
            echo 'Authentication required.';
            exit;
        }
    }
}
