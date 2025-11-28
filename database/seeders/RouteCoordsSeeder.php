<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RouteCoordsSeeder extends Seeder
{
    public function run(): void
    {
        // Migration defines columns: id, latitude(decimal), longitude(decimal), type(string), route_id, timestamps
        DB::table('routecoords')->insert([
            ['route_id' => 1, 'latitude' => -12.0560000, 'longitude' => -77.0850000, 'type' => 'point', 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => 1, 'latitude' => -12.0570000, 'longitude' => -77.0860000, 'type' => 'point', 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => 2, 'latitude' => -12.0600000, 'longitude' => -77.0900000, 'type' => 'point', 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => 2, 'latitude' => -12.0610000, 'longitude' => -77.0910000, 'type' => 'point', 'created_at' => now(), 'updated_at' => now()],
            ['route_id' => 3, 'latitude' => -12.0500000, 'longitude' => -77.0800000, 'type' => 'point', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
