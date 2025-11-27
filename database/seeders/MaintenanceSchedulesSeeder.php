<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceSchedulesSeeder extends Seeder
{
    public function run(): void
    {
        // Migration columns: maintenance_id, vehicle_id, driver_id, start_time, end_time, day_of_week, maintenance_type, timestamps
        $vehicleId = \App\Models\Vehicle::first()?->id ?? 1;
        $driverId = \App\Models\Employee::first()?->id ?? 1;

        DB::table('maintenanceschedules')->insert([
            ['maintenance_id' => 1, 'vehicle_id' => $vehicleId, 'driver_id' => $driverId, 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'day_of_week' => 'Lunes', 'maintenance_type' => 'preventive', 'created_at' => now(), 'updated_at' => now()],
            ['maintenance_id' => 2, 'vehicle_id' => $vehicleId, 'driver_id' => $driverId, 'start_time' => '09:00:00', 'end_time' => '13:00:00', 'day_of_week' => 'Martes', 'maintenance_type' => 'preventive', 'created_at' => now(), 'updated_at' => now()],
            ['maintenance_id' => 3, 'vehicle_id' => $vehicleId, 'driver_id' => $driverId, 'start_time' => '10:00:00', 'end_time' => '14:00:00', 'day_of_week' => 'Miércoles', 'maintenance_type' => 'corrective', 'created_at' => now(), 'updated_at' => now()],
            ['maintenance_id' => 4, 'vehicle_id' => $vehicleId, 'driver_id' => $driverId, 'start_time' => '11:00:00', 'end_time' => '15:00:00', 'day_of_week' => 'Jueves', 'maintenance_type' => 'preventive', 'created_at' => now(), 'updated_at' => now()],
            ['maintenance_id' => 5, 'vehicle_id' => $vehicleId, 'driver_id' => $driverId, 'start_time' => '12:00:00', 'end_time' => '16:00:00', 'day_of_week' => 'Viernes', 'maintenance_type' => 'preventive', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
