<?php

use App\Config\Router\Router;


// Router::get('/{id}', 'HomeController:home', 'home');
// Router::prefix('/api');
// Router::put('/', 'HomeController:index');
// Router::post('/', 'HomeController:index');
// Router::delete('/', 'HomeController:index');
// Router::patch('/', 'HomeController:index');
// Router::get('/', function() {
//     view('my-view');
// });
// Router::get('/', function() {
//     Router::redirect('/test');
// });

Router::get('/', 'HomeController:index');

Router::run();

if (Router::error()) {
    view('404');
}
