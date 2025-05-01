<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $brands = Brand::all();
        $materials = Material::all();

        for ($i = 1; $i <= 20; $i++) {
            $brand = $brands->random();
            $material = $materials->random();

            Product::create([
                'code' => 'PM' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'brand_id' => $brand->id,
                'material_id' => $material->id,
                'price' => rand(500, 5000) / 10,
                'description' => "Kryt pod motor pro vozidla {$brand->name}. Vyrobeno z materiálu {$material->name}.",
            ]);
        }
    }
}
