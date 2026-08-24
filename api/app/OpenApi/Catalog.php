<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'Online Store Catalog API', version: 'v1')]
#[OA\Server(url: '/', description: 'Default')]
#[OA\Tag(name: 'Каталог', description: 'Каталог')]
final class Catalog
{
}
