<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Zone;
use App\Models\Shift;
use App\Models\Vehicle;

class EmployeeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zone = Zone::first();
        $shift = Shift::first();
        $vehicle = Vehicle::first();

        // Fallbacks
        $zoneId = $zone ? $zone->id : 1;
        $shiftId = $shift ? $shift->id : 1;
        $vehicleId = $vehicle ? $vehicle->id : 1;

        DB::table('employeegroups')->insert([
            [
                'name' => 'Grupo Alpha',
                'zone_id' => $zoneId,
                'shift_id' => $shiftId,
                'vehicle_id' => $vehicleId,
                'days' => 'Lunes, Miércoles, Viernes',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Grupo Beta',
                'zone_id' => $zoneId,
                'shift_id' => $shiftId,
                'vehicle_id' => $vehicleId,
                'days' => 'Martes, Jueves, Sábado',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
