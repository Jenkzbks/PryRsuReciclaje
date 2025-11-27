<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigGroupsSeeder extends Seeder
{
    public function run(): void
    {
        // Asignar empleados a todos los grupos según la capacidad de su vehículo
        $groups = \App\Models\Employeegroup::all();
        $conductores = \App\Models\Employee::whereHas('type', function($q){ $q->where('name', 'like', '%conduc%'); })->get();
        $ayudantes = \App\Models\Employee::whereHas('type', function($q){ $q->where('name', 'like', '%ayud%'); })->get();
        foreach ($groups as $group) {
            $vehicle = \App\Models\Vehicle::find($group->vehicle_id);
            $capacity = $vehicle->capacity ?? $vehicle->passengers ?? 4;
            $data = [];
            // Posición 1: conductor
            $conductor = $conductores->shift();
            if ($conductor) {
                $data[] = [
                    'employeegroup_id' => $group->id,
                    'employee_id' => $conductor->id,
                    'posicion' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // Siguientes posiciones: ayudantes
            for ($i = 2; $i <= $capacity; $i++) {
                $ayudante = $ayudantes->shift();
                if ($ayudante) {
                    $data[] = [
                        'employeegroup_id' => $group->id,
                        'employee_id' => $ayudante->id,
                        'posicion' => $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            DB::table('configgroups')->insert($data);
        }
    }
}
