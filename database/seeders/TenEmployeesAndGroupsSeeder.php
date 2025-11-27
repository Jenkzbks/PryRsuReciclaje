<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\Contract;
use App\Models\Employeegroup;
use App\Models\Zone;
use App\Models\Shift;
use App\Models\Vehicle;
use Carbon\Carbon;

class TenEmployeesAndGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure types exist
        $conductorType = EmployeeType::firstOrCreate(['name' => 'Conductor'], ['description' => 'Conductor de vehículo']);
        $ayudanteType  = EmployeeType::firstOrCreate(['name' => 'Ayudante'], ['description' => 'Ayudante de recolección']);

        // Create 10 employees: 4 conductores, 6 ayudantes
        $employees = [];
        for ($i = 1; $i <= 10; $i++) {
            $isDriver = $i <= 4; // first 4 are drivers
            $dni = str_pad((string)(90000000 + $i), 8, '0', STR_PAD_LEFT);
            $names = $isDriver ? "Conductor {$i}" : "Ayudante {$i}";
            $lastnames = "Prueba {$i}";

            $emp = Employee::firstOrCreate([
                'dni' => $dni
            ], [
                'names' => $names,
                'lastnames' => $lastnames,
                'email' => "user{$i}@test.local",
                'phone' => '9' . rand(10000000, 99999999),
                'address' => 'Dirección de prueba',
                'birthday' => Carbon::now()->subYears(25 + ($i % 10))->format('Y-m-d'),
                'license' => $isDriver ? 'A2' : null,
                'type_id' => $isDriver ? $conductorType->id : $ayudanteType->id,
                'status' => 1,
                'password' => 'password',
                'photo' => 'default.jpg'
            ]);

            $employees[] = $emp;

            // Create a contract for each employee if not exists
            if (!Contract::where('employee_id', $emp->id)->exists()) {
                Contract::create([
                    'employee_id' => $emp->id,
                    'contrato_type' => Contract::TYPE_PERMANENTE ?? 'permanente',
                    'start_date' => Carbon::now()->subMonths(6)->format('Y-m-d'),
                    'end_date' => null,
                    'salary' => 1200 + ($isDriver ? 500 : 0),
                    'position_id' => $emp->type_id,
                    'departament_id' => 1,
                    'vacations_days_per_year' => 30,
                    'probation_period_months' => 2,
                    'is_active' => true,
                    'termination_reason' => ''
                ]);
            }
        }

        // Create at least 3 groups
        $zone = Zone::first();
        $shift = Shift::first();
        $vehicle = Vehicle::first();

        $zoneId = $zone?->id ?? 1;
        $shiftId = $shift?->id ?? 1;
        $vehicleId = $vehicle?->id ?? 1;

        $groupNames = ['Grupo Uno', 'Grupo Dos', 'Grupo Tres'];
        $createdGroups = [];
        foreach ($groupNames as $gname) {
            $g = Employeegroup::firstOrCreate([
                'name' => $gname
            ], [
                'zone_id' => $zoneId,
                'shift_id' => $shiftId,
                'vehicle_id' => $vehicleId,
                'days' => 'Lunes,Miércoles,Viernes',
                'status' => 1
            ]);
            $createdGroups[] = $g;
        }

        // Assign employees to groups (one driver + two ayudantes per group)
        $driverPool = array_filter($employees, fn($e) => $e->type_id == $conductorType->id);
        $assistantPool = array_filter($employees, fn($e) => $e->type_id == $ayudanteType->id);

        $driverPool = array_values($driverPool);
        $assistantPool = array_values($assistantPool);

        $dIndex = 0;
        $aIndex = 0;
        foreach ($createdGroups as $group) {
            // conductor
            if (isset($driverPool[$dIndex])) {
                DB::table('configgroups')->updateOrInsert([
                    'employeegroup_id' => $group->id,
                    'employee_id' => $driverPool[$dIndex]->id,
                ], [
                    'posicion' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            // ayudante 1
            if (isset($assistantPool[$aIndex])) {
                DB::table('configgroups')->updateOrInsert([
                    'employeegroup_id' => $group->id,
                    'employee_id' => $assistantPool[$aIndex]->id,
                ], [
                    'posicion' => 2,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            // ayudante 2
            if (isset($assistantPool[$aIndex + 1])) {
                DB::table('configgroups')->updateOrInsert([
                    'employeegroup_id' => $group->id,
                    'employee_id' => $assistantPool[$aIndex + 1]->id,
                ], [
                    'posicion' => 3,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $dIndex++;
            $aIndex += 2;
        }

        $this->command->info('✅ Ten employees with contracts and 3 groups created');
    }
}
