<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneVehicleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('zonevehicle')->insert([
            ['zone_id' => 1, 'vehicle_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 1, 'vehicle_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 2, 'vehicle_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 2, 'vehicle_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 3, 'vehicle_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
