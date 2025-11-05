<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BrandModel;
use App\Models\Brand;

class BrandModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            // Toyota
            ['name' => 'Corolla', 'description' => 'Sedán compacto.', 'brand_id' => Brand::where('name', 'Toyota')->first()->id],
            ['name' => 'Camry', 'description' => 'Sedán mediano.', 'brand_id' => Brand::where('name', 'Toyota')->first()->id],
            ['name' => 'Hilux', 'description' => 'Camioneta pickup.', 'brand_id' => Brand::where('name', 'Toyota')->first()->id],
            // Ford
            ['name' => 'F-150', 'description' => 'Pickup robusta.', 'brand_id' => Brand::where('name', 'Ford')->first()->id],
            ['name' => 'Mustang', 'description' => 'Deportivo icónico.', 'brand_id' => Brand::where('name', 'Ford')->first()->id],
            ['name' => 'Explorer', 'description' => 'SUV familiar.', 'brand_id' => Brand::where('name', 'Ford')->first()->id],
            // Honda
            ['name' => 'Civic', 'description' => 'Sedán deportivo.', 'brand_id' => Brand::where('name', 'Honda')->first()->id],
            ['name' => 'CR-V', 'description' => 'SUV compacto.', 'brand_id' => Brand::where('name', 'Honda')->first()->id],
            // Chevrolet
            ['name' => 'Silverado', 'description' => 'Pickup americana.', 'brand_id' => Brand::where('name', 'Chevrolet')->first()->id],
            ['name' => 'Malibu', 'description' => 'Sedán elegante.', 'brand_id' => Brand::where('name', 'Chevrolet')->first()->id],
            // Nissan
            ['name' => 'Altima', 'description' => 'Sedán confiable.', 'brand_id' => Brand::where('name', 'Nissan')->first()->id],
            ['name' => 'Sentra', 'description' => 'Sedán económico.', 'brand_id' => Brand::where('name', 'Nissan')->first()->id],
        ];

        foreach ($models as $model) {
            BrandModel::create($model);
        }
    }
}
