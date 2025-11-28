<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneShiftSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('zoneshift')->insert([
            ['zone_id' => 1, 'shift_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 1, 'shift_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 2, 'shift_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 2, 'shift_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['zone_id' => 3, 'shift_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
