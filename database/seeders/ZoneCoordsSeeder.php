<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneCoordsSeeder extends Seeder
{
    public function run(): void
    {
        // Migration defines columns: id, latitude, longitude, zone_id, timestamps
        DB::table('zonecoords')->insert([
            ['zone_id' => 1, 'latitude' => -12.056, 'longitude' => -77.085, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 1, 'latitude' => -12.057, 'longitude' => -77.086, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 2, 'latitude' => -12.060, 'longitude' => -77.090, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 2, 'latitude' => -12.061, 'longitude' => -77.091, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 3, 'latitude' => -12.050, 'longitude' => -77.080, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
