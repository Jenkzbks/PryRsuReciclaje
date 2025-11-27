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
        $zones = Zone::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();

        $groups = [
            [ 'name' => 'Grupo Alpha', 'days' => 'Lunes,Miércoles,Viernes' ],
            [ 'name' => 'Grupo Beta', 'days' => 'Martes,Jueves,Sábado' ],
            [ 'name' => 'Grupo Gamma', 'days' => 'Sábado' ],
        ];

        foreach ($groups as $i => $group) {
            $zone = $zones[$i] ?? $zones->first();
            $vehicle = $vehicles[$i] ?? $vehicles->first();
            $shift = $shifts[$i] ?? $shifts->first();
            $vehicleId = $vehicle ? $vehicle->id : 1;
            $shiftId = $shift ? $shift->id : 1;
            // Capacidad: asume campo 'capacity' o 'passengers', si no existe, usa 4 por defecto
            $capacity = $vehicle->capacity ?? $vehicle->passengers ?? 4;
            DB::table('employeegroups')->insert([
                [
                    'name' => $group['name'],
                    'zone_id' => $zone ? $zone->id : 1,
                    'shift_id' => $shiftId,
                    'vehicle_id' => $vehicleId,
                    'days' => $group['days'],
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
