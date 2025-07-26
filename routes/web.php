<?php

use App\Config\Router\Router;

// Router::prefix('/api/v1');

Router::get('/', 'HomeController:home');

Router::run();

if (Router::error()) {
    http_response_code(404);
    view('404');
}
