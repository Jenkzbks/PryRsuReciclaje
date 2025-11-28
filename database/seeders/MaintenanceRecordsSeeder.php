<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceRecordsSeeder extends Seeder
{
    public function run(): void
    {
        // Migration columns: schedule_id, maintenance_date, descripcion, estado, image_url
        $scheduleId = 1;

        DB::table('maintenancerecords')->insert([
            ['schedule_id' => $scheduleId, 'maintenance_date' => Carbon::now()->subDays(10)->format('Y-m-d'), 'descripcion' => 'Cambio de aceite realizado', 'estado' => true, 'image_url' => '', 'created_at' => now(), 'updated_at' => now()],
            ['schedule_id' => $scheduleId, 'maintenance_date' => Carbon::now()->subDays(5)->format('Y-m-d'), 'descripcion' => 'Frenos ajustados', 'estado' => true, 'image_url' => '', 'created_at' => now(), 'updated_at' => now()],
            ['schedule_id' => $scheduleId, 'maintenance_date' => Carbon::now()->subDays(2)->format('Y-m-d'), 'descripcion' => 'Llantas reemplazadas', 'estado' => true, 'image_url' => '', 'created_at' => now(), 'updated_at' => now()],
            ['schedule_id' => $scheduleId, 'maintenance_date' => Carbon::now()->subDays(1)->format('Y-m-d'), 'descripcion' => 'Revisión general', 'estado' => true, 'image_url' => '', 'created_at' => now(), 'updated_at' => now()],
            ['schedule_id' => $scheduleId, 'maintenance_date' => Carbon::now()->subDays(3)->format('Y-m-d'), 'descripcion' => 'Alineación hecha', 'estado' => true, 'image_url' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
