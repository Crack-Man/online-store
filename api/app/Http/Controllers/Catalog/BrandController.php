<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\BrandListResource;
use App\Models\Brand;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class BrandController extends Controller
{
    #[OA\Get(
        path: '/api/v1/catalog/brands',
        description: 'Получить все активные бренды',
        tags: ['Каталог'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список брендов',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/BrandListResource')
                )
            ),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        $brands = Brand::active()->get();

        return BrandListResource::collection($brands);
    }
}
