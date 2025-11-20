<?php
declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Scaffold PHP API',
    description: 'Basic health check endpoint.'
)]
#[OA\SecurityScheme(
    securityScheme: 'basicAuth',
    type: 'http',
    scheme: 'basic',
    description: 'Basic authentication for Swagger documentation.'
)]
class OpenApi
{
}
