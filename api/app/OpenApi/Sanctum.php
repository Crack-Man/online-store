<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/sanctum/csrf-cookie',
    summary: 'Get CSRF cookie',
    description: 'Initializes the browser session and sets the XSRF-TOKEN cookie. Swagger UI calls this endpoint automatically before registration.',
    tags: ['Session'],
    responses: [
        new OA\Response(
            response: 204,
            description: 'CSRF cookie initialized',
            headers: [
                new OA\Header(
                    header: 'Set-Cookie',
                    description: 'Contains the XSRF-TOKEN and session cookies.',
                    schema: new OA\Schema(type: 'string')
                ),
            ]
        ),
    ]
)]
final class Sanctum {}
