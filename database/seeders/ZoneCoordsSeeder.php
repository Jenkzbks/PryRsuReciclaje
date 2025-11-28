<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneCoordsSeeder extends Seeder
{
    public function run(): void
    {
        // Coordenadas aproximadas de Jose Leonardo Ortiz (Chiclayo, Perú)
        DB::table('coords')->insert([
            // Zona 1 (Norte)
            ['zone_id' => 1, 'coord_index' => 1, 'latitude' => -6.7570, 'longitude' => -79.8400, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 1, 'coord_index' => 2, 'latitude' => -6.7555, 'longitude' => -79.8350, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 1, 'coord_index' => 3, 'latitude' => -6.7530, 'longitude' => -79.8380, 'created_at' => now(), 'updated_at' => now()],
            // Zona 2 (Sur)
            ['zone_id' => 2, 'coord_index' => 1, 'latitude' => -6.7650, 'longitude' => -79.8450, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 2, 'coord_index' => 2, 'latitude' => -6.7630, 'longitude' => -79.8410, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 2, 'coord_index' => 3, 'latitude' => -6.7610, 'longitude' => -79.8440, 'created_at' => now(), 'updated_at' => now()],
            // Zona 3 (Centro)
            ['zone_id' => 3, 'coord_index' => 1, 'latitude' => -6.7600, 'longitude' => -79.8370, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 3, 'coord_index' => 2, 'latitude' => -6.7580, 'longitude' => -79.8340, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 3, 'coord_index' => 3, 'latitude' => -6.7590, 'longitude' => -79.8320, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
