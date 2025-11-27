<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\District;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have a district
        $district = District::first();
        $districtId = $district ? $district->id : 1;

        DB::table('zones')->insert([
            [
                'name' => 'Zona Norte',
                'area' => 50.5,
                'description' => 'Zona norte de la ciudad',
                'polygon_coordinates' => json_encode([]),
                'district_id' => $districtId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Zona Sur',
                'area' => 45.2,
                'description' => 'Zona sur de la ciudad',
                'polygon_coordinates' => json_encode([]),
                'district_id' => $districtId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Zona Centro',
                'area' => 30.0,
                'description' => 'Centro histórico',
                'polygon_coordinates' => json_encode([]),
                'district_id' => $districtId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
