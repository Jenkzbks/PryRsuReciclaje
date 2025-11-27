<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiclesImageSeeder extends Seeder
{
    public function run(): void
    {
        // Migration defines columns: id, image, profile, vehicle_id, timestamps
        DB::table('vehiclesimage')->insert([
            ['vehicle_id' => 1, 'image' => 'vehicles/vehicle1.jpg', 'profile' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_id' => 1, 'image' => 'vehicles/vehicle1_side.jpg', 'profile' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_id' => 2, 'image' => 'vehicles/vehicle2.jpg', 'profile' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_id' => 2, 'image' => 'vehicles/vehicle2_rear.jpg', 'profile' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_id' => 3, 'image' => 'vehicles/vehicle3.jpg', 'profile' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
