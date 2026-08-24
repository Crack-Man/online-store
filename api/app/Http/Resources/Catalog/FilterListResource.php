<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FilterListResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Память (ОЗУ)'),
        new OA\Property(property: 'slug', type: 'string', example: 'pamiat-ozu'),
        new OA\Property(property: 'type', type: 'string', example: 'string'),
        new OA\Property(property: 'measure', type: 'string', nullable: true, example: 'ГБ'),
        new OA\Property(property: 'values', type: 'array', items: new OA\Items(type: 'string')),
    ]
)]
class FilterListResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['id'] ?? $this->id,
            'name' => $this['name'] ?? $this->name,
            'slug' => $this['slug'] ?? $this->slug,
            'type' => $this['type'] ?? $this->type,
            'measure' => $this['measure'] ?? null,
            'values' => $this['values'] ?? [],
        ];
    }
}
