<?php
declare(strict_types=1);

namespace App\Controllers;



class HomeController
{
    public function home(\App\Config\Request\Request $request): void
    {
            echo "Welcome to the Home Page!\n";

            // (new \App\Config\Response\Response())->view(
            //     'home',
            //     [
            //         'data' => \$data
            //     ]
            // );
    }

    public function blog(\App\Config\Request\Request $request): void
    {
        $params = $request->getRouteParams();
        echo "Blog post {$params['id']} - {$params['slug']}\n";
    }

    public function update(\App\Config\Request\Request $request): void
    {
        $params = $request->getRouteParams();
        echo "Updated post {$params['id']}\n";
    }

    public function partialUpdate(\App\Config\Request\Request $request): void
    {
        $params = $request->getRouteParams();
        echo "Partially updated post {$params['id']}\n";
    }

    public function delete(\App\Config\Request\Request $request): void
    {
        $params = $request->getRouteParams();
        echo "Deleted post {$params['id']}\n";
    }

    public function contact(): void
    {
        echo "Contact page\n";
    }
}
