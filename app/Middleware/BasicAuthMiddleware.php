<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Config\Request\Request;
use App\Config\Response\HttpStatus;
use App\Config\Response\Response;

class BasicAuthMiddleware
{
    public function __invoke(Request $request): void
    {
        $username = getenv('SWAGGER_USERNAME') ?: null;
        $password = getenv('SWAGGER_PASSWORD') ?: null;

        if ($username === null || $password === null) {
            (new Response())->send(
                'Swagger credentials are not configured.',
                HttpStatus::INTERNAL_SERVER_ERROR
            );
            exit;
        }

        $providedUser = $_SERVER['PHP_AUTH_USER'] ?? null;
        $providedPassword = $_SERVER['PHP_AUTH_PW'] ?? null;

        if ($providedUser !== $username || $providedPassword !== $password) {
            header('WWW-Authenticate: Basic realm="Swagger Documentation"');
            (new Response())->send('Authentication required.', HttpStatus::UNAUTHORIZED);
            exit;
        }
    }
}
