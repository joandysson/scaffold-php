<?php

use App\Config\Router\Router;
use App\Config\Request\Request;
use App\Config\Response\HttpStatus;
use App\Config\Response\Response;

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

Router::get('/swagger.json', function () {
    $spec = [
        'openapi' => '3.0.3',
        'info' => [
            'title' => 'Scaffold PHP API',
            'version' => '1.0.0',
            'description' => 'Basic health check endpoint.',
        ],
        'paths' => [
            '/health' => [
                'get' => [
                    'summary' => 'Health check',
                    'tags' => ['Health'],
                    'responses' => [
                        '200' => [
                            'description' => 'Application is healthy.',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'status' => [
                                                'type' => 'string',
                                                'example' => 'ok',
                                            ],
                                        ],
                                        'required' => ['status'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'security' => [
                        ['basicAuth' => []],
                    ],
                ],
            ],
        ],
        'components' => [
            'securitySchemes' => [
                'basicAuth' => [
                    'type' => 'http',
                    'scheme' => 'basic',
                ],
            ],
        ],
    ];

    return (new Response())->json($spec);
});

Router::get('/swagger', function () {
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
