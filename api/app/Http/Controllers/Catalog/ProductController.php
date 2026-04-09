<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\Catalog\ProductService;
use App\Http\Resources\Catalog\ProductListResource;
use App\Http\Resources\Catalog\FilterListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Catalog\GetProductsRequest;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    private const DEFAULT_PER_PAGE = 12;

    #[OA\Post(
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
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(
                        property: "filters",
                        type: "array",
                        items: new OA\Items(
                            type: "object",
                            properties: [
                                new OA\Property(property: "property_slug", type: "string", example: "pamiat-ozu"),
                                new OA\Property(property: "value", type: "string", example: "black"),
                                new OA\Property(property: "min", type: "number", example: 8),
                                new OA\Property(property: "max", type: "number", example: 16)
                            ]
                        )
                    )
                ],
                example: [
                    "filters" => [
                        [
                            "property_slug" => "pamiat-pzu",
                            "value" => "512"
                        ],
                        [
                            "property_slug" => "displei",
                            "min" => 6.1,
                            "max" => 6.1
                        ],
                    ],
                ]
            )
        ),
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
    public function index(GetProductsRequest $request, string $slug, ProductService $productService): AnonymousResourceCollection
    {
        $validatedRequest = $request->validated();
        $filters = $validatedRequest['filters'] ?? [];
        $brand = Brand::where('slug', $slug)->firstOrFail();
        $products = $productService->filterProducts($filters, $brand)->paginate(self::DEFAULT_PER_PAGE);
        
        return ProductListResource::collection($products);
    }

    #[OA\Get(
        path: '/api/v1/catalog/{brandSlug}/filters',
        description: 'Получить все доступные фильтры по бренду',
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
        ],
        tags: ['Catalog'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список фильтров',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/FilterListResource')
                )
            )
        ]
    )]
    public function getFilters(Request $request, string $slug, ProductService $productService): AnonymousResourceCollection
    {
        $filters = $request->validate([]);
        
        $brand = Brand::where('slug', $slug)->firstOrFail();
        $filters = $productService->getFilters($brand);
        
        return FilterListResource::collection($filters);
    }
}
