<?php

namespace App\Controllers;

use App\Service\PostService;

class HomeController
{
    public function home(): void
    {
            echo "Welcome to the Home Page!\n";

            // view('home', [
            //     'data' => $data
            // ]);
    }
}
