<?php
declare(strict_types=1);

namespace App\Controller;

use App\Config\Response\Response;
use App\Config\Response\HttpStatus;

class HealthController
{
    public function show(): Response
    {
        return (new Response())->json(
            ['status' => 'ok'],
            HttpStatus::OK
        );
    }
}
