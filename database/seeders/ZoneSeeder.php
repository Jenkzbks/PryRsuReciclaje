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
        // Buscar el distrito 'Jose Leonardo Ortiz'
        $district = District::where('name', 'like', '%jose leonardo ortiz%')->first();
        $districtId = $district ? $district->id : 1;

        DB::table('zones')->insert([
            [
                'name' => 'Zona Norte',
                'average_waste' => 12.5,
                'description' => 'Zona norte de Jose Leonardo Ortiz',
                'status' => 1,
                'district_id' => $districtId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Zona Sur',
                'average_waste' => 10.2,
                'description' => 'Zona sur de Jose Leonardo Ortiz',
                'status' => 1,
                'district_id' => $districtId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Zona Centro',
                'average_waste' => 8.0,
                'description' => 'Centro de Jose Leonardo Ortiz',
                'status' => 1,
                'district_id' => $districtId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
