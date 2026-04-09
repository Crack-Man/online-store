<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Property;
use Illuminate\Database\Seeder;
use RuntimeException;

class FilterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataPath = database_path('seeders/data/filters.json');
        $json = file_get_contents($dataPath);

        if ($json === false) {
            throw new RuntimeException("Cannot read seed data: {$dataPath}");
        }

        $json = preg_replace('/^\s*\/\/.*$/m', '', $json);

        if ($json === null) {
            throw new RuntimeException("Cannot preprocess seed data: {$dataPath}");
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $filters = $data['filters'] ?? [];

        foreach ($filters as $filter) {
            $brand = Brand::where('name', $filter['brand'])->first();
            if (!$brand) {
                echo "Brand {$filter['brand']} not found\n";
                continue;
            }

            $propertySlugs = $filter['properties'] ?? [];

            $caseParts = [];
            $bindings = [];

            foreach (array_values($propertySlugs) as $index => $slug) {
                $caseParts[] = "WHEN ? THEN {$index}";
                $bindings[] = $slug;
            }

            $properties = Property::whereIn('slug', $propertySlugs)
                ->orderByRaw('CASE slug ' . implode(' ', $caseParts) . ' ELSE 999999 END', $bindings)
                ->get();
            $brand->update(['filter_properties' => $properties->pluck('id')->toArray()]);
        }
    }
}
