<?php

namespace App\Controllers;

use App\Service\PostService;

class HomeController
{
    public function home(\App\Config\Request\Request $request): void
    {
            echo "Welcome to the Home Page!\n";

            // view('home', [
            //     'data' => $data
            // ]);
    }
}
