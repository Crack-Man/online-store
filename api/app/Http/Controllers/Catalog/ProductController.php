<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\ProductListResource;
use App\Services\Catalog\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    private const DEFAULT_PER_PAGE = 12;

    #[OA\Get(
        path: '/api/v1/catalog/{brandSlug}',
        description: 'Получить все активные товары',
        parameters: [
            new OA\Parameter(
                name: "brandSlug",
                description: "Слаг бренда",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "string",
                    example: "apple"
                )
            ),
            new OA\Parameter(
                name: "page",
                description: "Номер страницы",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: "integer",
                    example: 1
                )
            ),
        ],
        tags: ['Catalog'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список товаров',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/ProductListResource')
                )
            )
        ]
    )]
    public function index(Request $request, String $brandSlug, ProductService $productService): AnonymousResourceCollection
    {
        $filters = $request->validate([]);
        $products = $productService->filterProducts($filters, $brandSlug)->paginate(self::DEFAULT_PER_PAGE);
        
        return ProductListResource::collection($products);
    }
}
