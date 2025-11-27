<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\VehicleType;
use App\Models\Color;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brand = Brand::first();
        $model = BrandModel::first();
        $type = VehicleType::first();
        $color = Color::first();

        // Fallbacks if seeders haven't run or are empty (though they should be run before this)
        $brandId = $brand ? $brand->id : 1;
        $modelId = $model ? $model->id : 1;
        $typeId = $type ? $type->id : 1;
        $colorId = $color ? $color->id : 1;

        DB::table('vehicles')->insert([
            [
                'name' => 'Camión Recolector 01',
                'code' => 'CR-001',
                'plate' => 'ABC-123',
                'year' => 2020,
                'load_capacity' => 5000,
                'description' => 'Camión principal de recolección',
                'status' => 1,
                'passengers' => 3,
                'fuel_capacity' => 100,
                'brand_id' => $brandId,
                'model_id' => $modelId,
                'type_id' => $typeId,
                'color_id' => $colorId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Camión Recolector 02',
                'code' => 'CR-002',
                'plate' => 'XYZ-789',
                'year' => 2021,
                'load_capacity' => 5500,
                'description' => 'Camión secundario',
                'status' => 1,
                'passengers' => 3,
                'fuel_capacity' => 110,
                'brand_id' => $brandId,
                'model_id' => $modelId,
                'type_id' => $typeId,
                'color_id' => $colorId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Camioneta Supervisión',
                'code' => 'CS-001',
                'plate' => 'SUP-001',
                'year' => 2022,
                'load_capacity' => 1000,
                'description' => 'Vehículo para supervisores',
                'status' => 1,
                'passengers' => 5,
                'fuel_capacity' => 60,
                'brand_id' => $brandId,
                'model_id' => $modelId,
                'type_id' => $typeId,
                'color_id' => $colorId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
