<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have a zone to reference
        $zone = \App\Models\Zone::first();
        $zoneId = $zone ? $zone->id : 1;

        DB::table('routes')->insert([
            ['name' => 'Ruta Central 1', 'description' => 'Recorrido por el centro', 'zone_id' => $zoneId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ruta Sur 1', 'description' => 'Recorrido por el sur', 'zone_id' => $zoneId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ruta Norte 1', 'description' => 'Recorrido por el norte', 'zone_id' => $zoneId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ruta Este 1', 'description' => 'Recorrido por el este', 'zone_id' => $zoneId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ruta Oeste 1', 'description' => 'Recorrido por el oeste', 'zone_id' => $zoneId, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
