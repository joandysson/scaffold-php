<?php
declare(strict_types=1);

namespace App\Middleware;

use Config\Request\Request;
use Config\Response\HttpStatus;
use Config\Response\Response;

class BasicAuthMiddleware
{
    public function __invoke(Request $request): ?Response
    {
        $username = getenv('SWAGGER_USERNAME') ?: null;
        $password = getenv('SWAGGER_PASSWORD') ?: null;

        if ($username === null || $password === null) {
            return (new Response())->json(
                ['error' => 'Swagger credentials are not configured.'],
                HttpStatus::INTERNAL_SERVER_ERROR
            );
        }

        $providedUser = $_SERVER['PHP_AUTH_USER'] ?? null;
        $providedPassword = $_SERVER['PHP_AUTH_PW'] ?? null;

        if ($providedUser !== $username || $providedPassword !== $password) {
            header('WWW-Authenticate: Basic realm="Swagger Documentation"');
            return (new Response())->json(['error' => 'Authentication required.'], HttpStatus::UNAUTHORIZED);
        }

        return null;
    }
}
