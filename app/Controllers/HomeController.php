<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Config\Request\Request;

class HomeController
{
    public function home(Request $request): void
    {
        xdebug_break();
        xdebug_info(); exit;
            echo "Welcome to the Home Page!\n";

            // (new \App\Config\Response\Response())->view(
            //     'home',
            //     [
            //         'data' => \$data
            //     ]
            // );
    }

    public function blog(Request $request): void
    {
        $params = $request->getRouteParams();
        echo "Blog post {$params['id']} - {$params['slug']}\n";
    }

    public function update(Request $request): void
    {
        $params = $request->getRouteParams();
        echo "Updated post {$params['id']}\n";
    }

    public function partialUpdate(Request $request): void
    {
        $params = $request->getRouteParams();
        echo "Partially updated post {$params['id']}\n";
    }

    public function delete(Request $request): void
    {
        $params = $request->getRouteParams();
        echo "Deleted post {$params['id']}\n";
    }

    public function contact(): void
    {
        echo "Contact page\n";
    }
}
