<?php

namespace App\Services\Catalog;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function filterProducts(array $filters, ?Brand $brand = null)
    {
        $query = Product::query();

        if ($brand) {
            $query->whereHas('productGroup.brand', function ($query) use ($brand) {
                $query->where('id', $brand->id);
            });
        }

        return $query;
    }

    public function getFilters(Brand $brand): array
    {
        $propertyIds = $brand->filter_properties ?? [];

        if (!is_array($propertyIds) || $propertyIds === []) {
            return [];
        }

        $properties = $this->getOrderedProperties($propertyIds);
        $valuesByName = $this->getAggregatedPropertyValues($brand);

        return $this->buildFilterPayload($properties, $valuesByName);
    }

    /**
     * Возвращает свойства в том порядке, в котором они перечислены в filter_properties бренда.
     *
     * @param array<int> $propertyIds
     */
    private function getOrderedProperties(array $propertyIds)
    {
        $caseParts = [];
        $orderBindings = [];

        foreach (array_values($propertyIds) as $index => $id) {
            $caseParts[] = "WHEN ? THEN {$index}";
            $orderBindings[] = $id;
        }

        return Property::whereIn('id', $propertyIds)
            ->active()
            ->orderByRaw('CASE id ' . implode(' ', $caseParts) . ' ELSE 999999 END', $orderBindings)
            ->get();
    }

    /**
     * Собирает все значения свойств из JSON-поля products.properties для товаров выбранного бренда.
     *
     * На стороне PostgreSQL разворачиваем JSON-массив через jsonb_array_elements,
     * затем группируем значения по имени свойства.
     *
     * @return array<string, array<int, string>>
     */
    private function getAggregatedPropertyValues(Brand $brand): array
    {
        $rows = DB::select(
            "SELECT
                elem->>'name' AS property_name,
                array_agg(DISTINCT elem->>'value') AS all_values
            FROM products
            CROSS JOIN LATERAL jsonb_array_elements(properties) AS elem
            INNER JOIN product_groups ON product_groups.id = products.product_group_id
            WHERE product_groups.brand_id = ?
              AND products.is_active = TRUE
              AND products.price > 0
              AND product_groups.is_active = TRUE
              AND products.properties IS NOT NULL
            GROUP BY elem->>'name'",
            [$brand->id]
        );

        $valuesByName = [];

        foreach ($rows as $row) {
            $raw = trim((string) $row->all_values, '{}');
            $valuesByName[$row->property_name] = $raw !== '' ? str_getcsv($raw) : [];
        }

        return $valuesByName;
    }

    /**
     * Итоговый ответ для API получения фильтров.
     *
     * Для числовых фильтров в values кладём только диапазон: [min, max].
     * Для строковых фильтров кладём список уникальных значений.
     *
     * @param array<string, array<int, string>> $valuesByName
     */
    private function buildFilterPayload($properties, array $valuesByName): array
    {
        $data = [];

        foreach ($properties as $property) {
            $rawValues = $this->normalizePropertyValues($valuesByName[$property->name] ?? []);

            $data[] = [
                'id' => $property->id,
                'name' => $property->name,
                'slug' => $property->slug,
                'type' => $property->type,
                'values' => $this->formatPropertyValues($property->type, $rawValues),
            ];
        }

        return $data;
    }

    /**
     * Убирает пустые и служебные значения, которые не должны попасть в фильтры.
     *
     * @param array<int, mixed> $values
     * @return array<int, mixed>
     */
    private function normalizePropertyValues(array $values): array
    {
        return array_values(array_filter(
            $values,
            static function ($value) {
                return $value !== null && $value !== '' && $value !== 'NULL';
            }
        ));
    }

    /**
     * Приводит значения свойства к формату ответа API.
     *
     * @param array<int, mixed> $rawValues
     * @return array<int, float|string>
     */
    private function formatPropertyValues(string $type, array $rawValues): array
    {
        if ($type === "range") {
            return $this->getNumericRangeValues($rawValues);
        }

        $values = array_values(array_unique(array_map(
            static function ($value) {
                return (string) $value;
            },
            $rawValues
        )));

        sort($values);

        return $values;
    }

    /**
     * Возвращает диапазон числовых значений в формате [min, max].
     *
     * @param array<int, mixed> $rawValues
     * @return array<int, float>
     */
    private function getNumericRangeValues(array $rawValues): array
    {
        $numericValues = array_values(array_filter(
            array_map(static function ($value) {
                return (float) $value;
            }, $rawValues),
            static function ($value) {
                return is_finite($value);
            }
        ));

        if ($numericValues === []) {
            return [];
        }

        return [min($numericValues), max($numericValues)];
    }
}