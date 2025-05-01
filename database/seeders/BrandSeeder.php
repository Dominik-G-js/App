<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $brands = [
            'Audi', 'BMW', 'Ford', 'Škoda', 'Volkswagen', 'Mercedes-Benz',
            'Toyota', 'Honda', 'Hyundai', 'Kia'
        ];

        foreach ($brands as $brand) {
            Brand::create(['name' => $brand]);
        }
    }
}
