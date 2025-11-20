<?php
declare(strict_types=1);

namespace App\Controller;

use App\Config\Response\Response;
use App\Config\Response\HttpStatus;
use OpenApi\Attributes as OA;

class HealthController
{
    #[OA\Get(
        path: '/health',
        summary: 'Health check',
        tags: ['Health'],
        responses: [
            new OA\Response(
                response: HttpStatus::OK->value,
                description: 'Application is healthy.',
                content: new OA\JsonContent(
                    type: 'object',
                    required: ['status'],
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'string',
                            example: 'ok'
                        ),
                    ]
                )
            ),
        ],
        security: [
            ['basicAuth' => []],
        ]
    )]
    public function show(): Response
    {
        return (new Response())->json(
            ['status' => 'ok'],
            HttpStatus::OK
        );
    }
}
