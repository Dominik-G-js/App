<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run()
    {
        $materials = [
            'Plast', 'Plech', 'Hliník', 'Ocel', 'Kompozit'
        ];

        foreach ($materials as $material) {
            Material::create(['name' => $material]);
        }
    }
}

