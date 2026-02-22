<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\Property;
use Illuminate\Database\Seeder;
use RuntimeException;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataPath = database_path('seeders/data/catalog.json');
        $json = file_get_contents($dataPath);

        if ($json === false) {
            throw new RuntimeException("Cannot read seed data: {$dataPath}");
        }

        $json = preg_replace('/^\s*\/\/.*$/m', '', $json);

        if ($json === null) {
            throw new RuntimeException("Cannot preprocess seed data: {$dataPath}");
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $catalog = $data['catalog'] ?? [];
        $properties = $data['properties'] ?? [];

        foreach ($properties as $property) {
            Property::updateOrCreate(['name' => $property['name']], $property);
        }

        foreach ($catalog as $brandData) {
            $brandName = $brandData['brand_name'] ?? null;

            if (!is_string($brandName) || $brandName === '') {
                continue;
            }

            $brand = Brand::updateOrCreate(['name' => $brandName], ['name' => $brandName]);

            $groups = $brandData['product_groups'] ?? [];

            foreach ($groups as $groupData) {
                $groupName = $groupData['name'] ?? null;

                if (!is_string($groupName) || $groupName === '') {
                    continue;
                }

                $group = ProductGroup::updateOrCreate(
                    ['brand_id' => $brand->id, 'name' => $groupName],
                    ['brand_id' => $brand->id, 'name' => $groupName]
                );

                $products = $groupData['products'] ?? [];

                foreach ($products as $productData) {
                    $productName = $productData['name'] ?? null;

                    if (!is_string($productName) || $productName === '') {
                        continue;
                    }

                    $product = Product::updateOrCreate(
                        ['product_group_id' => $group->id, 'name' => $productName],
                        [
                            'product_group_id' => $group->id,
                            'name' => $productName,
                            'description' => $productData['description'] ?? null,
                            'price' => $productData['price'] ?? 0,
                            'properties' => $productData['properties'] ?? null,
                        ]
                    );

                    unset($product);
                }

                unset($group);
            }

            unset($brand);
        }
    }
}
