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
    }
}
