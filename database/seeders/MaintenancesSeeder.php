<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenancesSeeder extends Seeder
{
    public function run(): void
    {
        // Migration defines: id, name, start_date, end_date, timestamps
        DB::table('maintenances')->insert([
            ['name' => 'Cambio de aceite', 'start_date' => Carbon::now()->addDays(7)->format('Y-m-d'), 'end_date' => Carbon::now()->addDays(8)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Revisión de frenos', 'start_date' => Carbon::now()->addDays(10)->format('Y-m-d'), 'end_date' => Carbon::now()->addDays(11)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cambio de llantas', 'start_date' => Carbon::now()->addDays(15)->format('Y-m-d'), 'end_date' => Carbon::now()->addDays(15)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Revisión general', 'start_date' => Carbon::now()->addDays(20)->format('Y-m-d'), 'end_date' => Carbon::now()->addDays(21)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Alineación', 'start_date' => Carbon::now()->addDays(5)->format('Y-m-d'), 'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
