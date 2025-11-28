<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Toyota',
                'logo' => null,
                'description' => 'Marca japonesa líder en automóviles.',
            ],
            [
                'name' => 'Ford',
                'logo' => null,
                'description' => 'Marca estadounidense de vehículos.',
            ],
            [
                'name' => 'Honda',
                'logo' => null,
                'description' => 'Marca japonesa conocida por su calidad.',
            ],
            [
                'name' => 'Chevrolet',
                'logo' => null,
                'description' => 'Marca de General Motors.',
            ],
            [
                'name' => 'Nissan',
                'logo' => null,
                'description' => 'Marca japonesa con modelos innovadores.',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
