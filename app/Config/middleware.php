<?php
/**
 * Global middleware classes executed before matched routes.
 */
return [
    App\Middleware\CorsMiddleware::class,
    App\Middleware\SwaggerAuthMiddleware::class,
];
