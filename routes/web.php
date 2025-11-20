<?php

use App\Config\Router\Router;
use App\Config\Request\Request;
use App\Config\Response\HttpStatus;
use App\Config\Response\Response;
use App\Middleware\SwaggerAuthMiddleware;

// Middlewares are executed before each matched route.
// Register them using Router::addMiddleware().
// Example:
// Router::addMiddleware(function (Request $req) {
//     // Authentication or logging logic
// });

// Uncomment to prefix all routes with a base path
// Router::prefix('/api/v1');
// Basic GET route using a controller action
Router::get('/', 'HomeController:home');

// Route demonstrating path parameters
Router::get('/blog/{id}/{slug}', 'HomeController:blog');

// Closure route with dependency injection
Router::get('/hello/{name}', function (Request $request) {
    $params = $request->getRouteParams();
    echo "Hello {$params['name']}";
});

// POST route example
Router::post('/submit', function () {
    echo 'Form submitted';
});

Router::get('/health', 'HealthController:show');

Router::get('/swagger.json', function (Request $request) {
    (new SwaggerAuthMiddleware())($request);

    $swaggerPath = __DIR__ . '/../docs/swagger.json';

    if (!is_file($swaggerPath)) {
        return (new Response())->json(
            ['error' => 'Swagger document not found.'],
            HttpStatus::INTERNAL_SERVER_ERROR
        );
    }

    $contents = file_get_contents($swaggerPath);

    if ($contents === false) {
        return (new Response())->json(
            ['error' => 'Unable to load Swagger document.'],
            HttpStatus::INTERNAL_SERVER_ERROR
        );
    }

    $spec = json_decode($contents, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return (new Response())->json(
            ['error' => 'Swagger document is invalid JSON.'],
            HttpStatus::INTERNAL_SERVER_ERROR
        );
    }

    return (new Response())->json($spec);
});

Router::get('/swagger', function (Request $request) {
    (new SwaggerAuthMiddleware())($request);

    return (new Response())->view('swagger');
});

// PUT, PATCH and DELETE examples
Router::put('/posts/{id}', 'HomeController:update');
Router::patch('/posts/{id}', 'HomeController:partialUpdate');
Router::delete('/posts/{id}', 'HomeController:delete');

// Named route example
Router::get('/contact', 'HomeController:contact', 'contact.page');

Router::run();

if (Router::error()) {

    // (new Response)->view(
    //     'errors/404',
    //     ['message' => 'Page not found'],
    //     HttpStatus::NOT_FOUND
    // );

    (new Response)->json(
        ['error' => 'Not Found'],
        HttpStatus::NOT_FOUND
    )->send();
}
