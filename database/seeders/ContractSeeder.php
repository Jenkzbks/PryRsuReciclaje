<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Department;
use App\Models\EmployeeType;
use Carbon\Carbon;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurar que existe al menos un departamento
        $defaultDept = Department::firstOrCreate(
            ['name' => 'Departamento Prueba'],
            ['code' => 'DEP-PRUEBA']
        );

        // Buscar empleados creados por el seeder de personal (si existen)
        $emails = ['admin@personal.test', 'pedro@personal.test', 'maria@personal.test'];
        $employees = Employee::whereIn('email', $emails)->get();

        // Si no se encuentran por email, tomar los primeros 3 empleados
        if ($employees->isEmpty()) {
            $employees = Employee::orderBy('id')->take(3)->get();
        }

        if ($employees->isEmpty()) {
            $this->command->info('⚠️ No hay empleados para asignar contratos. Ejecuta primero el seeder de personal.');
            return;
        }

        foreach ($employees as $index => $employee) {
            // Evitar duplicados
            if (Contract::where('employee_id', $employee->id)->exists()) {
                continue;
            }

            // Valores por defecto según el índice (simular los 3 contratos de prueba)
            if ($index === 0) {
                $type = Contract::TYPE_PERMANENTE;
                $start = Carbon::now()->subMonths(24);
                $end = null;
                $salary = 3500.00;
                $vacDays = 30;
                $probation = 3;
            } elseif ($index === 1) {
                $type = Contract::TYPE_EVENTUAL;
                $start = Carbon::now()->subMonths(18);
                $end = Carbon::now()->addMonths(6);
                $salary = 1800.00;
                $vacDays = 15;
                $probation = 2;
            } else {
                $type = Contract::TYPE_PERMANENTE;
                $start = Carbon::now()->subMonths(12);
                $end = null;
                $salary = 1900.00;
                $vacDays = 20;
                $probation = 3;
            }

            // Determinar position_id: usar el type del empleado si existe, sino el primero disponible
            $positionId = $employee->type_id ?? EmployeeType::first()?->id;

            Contract::create([
                'employee_id' => $employee->id,
                'contrato_type' => $type,
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end ? $end->format('Y-m-d') : null,
                'salary' => $salary,
                'position_id' => $positionId,
                'departament_id' => $defaultDept->id,
                'vacations_days_per_year' => $vacDays,
                'probation_period_months' => $probation,
                'is_active' => true,
                'termination_reason' => ''
            ]);
        }

        $this->command->info('✅ Contracts seeded successfully.');
    }
}
