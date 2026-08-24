<?php

namespace App\Http\Resources\Catalog;

use App\Models\Property;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductDetailResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Apple iPhone 17 Pro'),
        new OA\Property(property: 'slug', type: 'string', example: 'apple-iphone-17-pro'),
        new OA\Property(property: 'price', type: 'number', example: 999.99),
        new OA\Property(
            property: 'properties',
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'Память (ОЗУ)'),
                    new OA\Property(property: 'slug', type: 'string', example: 'pamiat-ozu'),
                    new OA\Property(property: 'type', type: 'string', example: 'string'),
                    new OA\Property(property: 'value', type: 'string', example: '12'),
                    new OA\Property(property: 'measure', type: 'string', nullable: true, example: 'ГБ'),
                ]
            )
        ),
        new OA\Property(property: 'description', type: 'string', example: 'The latest Apple iPhone with advanced features and sleek design.'),
    ]
)]
class ProductDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        $properties = collect(is_array($this->properties) ? $this->properties : [])
            ->filter(fn ($property) => is_array($property) && isset($property['id']))
            ->values();
        $propertyModels = Property::with('unit')
            ->whereIn('id', $properties->pluck('id')->map(fn ($id) => (int) $id)->unique())
            ->get()
            ->keyBy('id');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'properties' => $properties->map(function (array $property) use ($propertyModels): array {
                $propertyModel = $propertyModels->get((int) $property['id']);

                if (! $propertyModel) {
                    return [
                        'id' => (int) $property['id'],
                        'value' => $property['value'] ?? null,
                    ];
                }

                return [
                    'id' => $propertyModel->id,
                    'name' => $propertyModel->name,
                    'slug' => $propertyModel->slug,
                    'type' => $propertyModel->type?->value ?? (string) $propertyModel->type,
                    'value' => $property['value'] ?? null,
                    'measure' => $propertyModel->unit?->symbol,
                ];
            })->all(),
            'description' => $this->description,
        ];
    }
}
