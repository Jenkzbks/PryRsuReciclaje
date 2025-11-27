<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\VehicleType;
use App\Models\Color;

class AddFiveVehiclesSeeder extends Seeder
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

        $brandId = $brand ? $brand->id : 1;
        $modelId = $model ? $model->id : 1;
        $typeId = $type ? $type->id : 1;
        $colorId = $color ? $color->id : 1;

        $now = now();

        DB::table('vehicles')->insert([
            [
                'name' => 'Compacta Recolectora 03',
                'code' => 'CR-003',
                'plate' => 'DEF-456',
                'year' => 2019,
                'load_capacity' => 3200,
                'description' => 'Camión compacto de recolección',
                'status' => 1,
                'passengers' => 3,
                'fuel_capacity' => 90,
                'brand_id' => $brandId,
                'model_id' => $modelId,
                'type_id' => $typeId,
                'color_id' => $colorId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Compacta Recolectora 04',
                'code' => 'CR-004',
                'plate' => 'GHI-101',
                'year' => 2018,
                'load_capacity' => 3000,
                'description' => 'Camión compacto de recolección',
                'status' => 1,
                'passengers' => 3,
                'fuel_capacity' => 85,
                'brand_id' => $brandId,
                'model_id' => $modelId,
                'type_id' => $typeId,
                'color_id' => $colorId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Volquete 01',
                'code' => 'VQ-001',
                'plate' => 'VQ-501',
                'year' => 2017,
                'load_capacity' => 8000,
                'description' => 'Volquete para retiros pesados',
                'status' => 1,
                'passengers' => 2,
                'fuel_capacity' => 150,
                'brand_id' => $brandId,
                'model_id' => $modelId,
                'type_id' => $typeId,
                'color_id' => $colorId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Camioneta Utilitaria 01',
                'code' => 'CU-001',
                'plate' => 'UTL-202',
                'year' => 2023,
                'load_capacity' => 800,
                'description' => 'Vehículo utilitario para logística',
                'status' => 1,
                'passengers' => 2,
                'fuel_capacity' => 55,
                'brand_id' => $brandId,
                'model_id' => $modelId,
                'type_id' => $typeId,
                'color_id' => $colorId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Camioneta Utilitaria 02',
                'code' => 'CU-002',
                'plate' => 'UTL-203',
                'year' => 2024,
                'load_capacity' => 900,
                'description' => 'Vehículo utilitario adicional',
                'status' => 1,
                'passengers' => 2,
                'fuel_capacity' => 60,
                'brand_id' => $brandId,
                'model_id' => $modelId,
                'type_id' => $typeId,
                'color_id' => $colorId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
