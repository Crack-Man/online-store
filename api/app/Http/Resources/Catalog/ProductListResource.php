<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
#[OA\Schema(
    schema: 'ProductListResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Apple iPhone 17 Pro'),
        new OA\Property(property: 'slug', type: 'string', example: 'apple-iphone-17-pro'),
        new OA\Property(property: 'price', type: 'number', example: 999.99),
    ]
)]
class ProductListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
        ];
    }
}
