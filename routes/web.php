<?php

use App\Config\Router\Router;
use App\Config\Request\Request;
use App\Config\Response\HttpStatus;
use App\Config\Response\Response;
use App\Middleware\BasicAuthMiddleware;

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

Router::middleware([BasicAuthMiddleware::class])->get('/api.json', function (Request $request) {
    $documentPath = __DIR__ . '/../docs/api.json';

    if (!is_file($documentPath)) {
        return (new Response())->json(
            ['error' => 'API document not found.'],
            HttpStatus::INTERNAL_SERVER_ERROR
        );
    }

    try {
        $spec = json_decode(file_get_contents($documentPath) ?: '', true, 512, JSON_THROW_ON_ERROR);
    } catch (\Throwable $exception) {
        return (new Response())->json(
            ['error' => 'Failed to load API document.'],
            HttpStatus::INTERNAL_SERVER_ERROR
        );
    }

    return (new Response())->json($spec);
});

Router::middleware([BasicAuthMiddleware::class])->get('/swagger', function (Request $request) {
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
