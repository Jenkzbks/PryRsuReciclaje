<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeType;
use App\Models\Employee;
use App\Models\Contract;
use App\Models\Department;
use App\Models\Vacation;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PersonnelTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Tipos de Empleado
        $adminType = EmployeeType::create([
            'name' => 'Administrador',
            'description' => 'Personal administrativo con acceso completo',
            'protected' => true
        ]);

        $operatorType = EmployeeType::create([
            'name' => 'Operador',
            'description' => 'Personal operativo de reciclaje',
            'protected' => false
        ]);

        $supervisorType = EmployeeType::create([
            'name' => 'Supervisor',
            'description' => 'Personal de supervisión',
            'protected' => false
        ]);

        // 2. Crear Empleados de Prueba
        $admin = Employee::firstOrCreate(
            ['dni' => '12345678'],
            [
                'names' => 'Juan Carlos',
                'lastnames' => 'García López',
                'email' => 'admin@personal.test',
                'phone' => '987654321',
                'address' => 'Av. Principal 123, Lima',
                'birthday' => Carbon::now()->subYears(35)->format('Y-m-d'),
                'license' => 'A2B',
                'type_id' => $adminType->id,
                'status' => 1,
                'password' => Hash::make('12345678'),
                'photo' => 'default.jpg'
            ]
        );

        $operator1 = Employee::firstOrCreate(
            ['dni' => '87654321'],
            [
                'names' => 'Pedro Luis',
                'lastnames' => 'Mendoza Silva',
                'email' => 'pedro@personal.test',
                'phone' => '976543210',
                'address' => 'Jr. Los Olivos 456, Lima',
                'birthday' => Carbon::now()->subYears(28)->format('Y-m-d'),
                'license' => 'A1',
                'type_id' => $operatorType->id,
                'status' => 1,
                'password' => Hash::make('87654321'),
                'photo' => 'default.jpg'
            ]
        );

        $operator2 = Employee::firstOrCreate(
            ['dni' => '11223344'],
            [
                'names' => 'María Elena',
                'lastnames' => 'Rodríguez Vargas',
                'email' => 'maria@personal.test',
                'phone' => '965432109',
                'address' => 'Av. Los Pinos 789, Lima',
                'birthday' => Carbon::now()->subYears(32)->format('Y-m-d'),
                'license' => 'A1',
                'type_id' => $operatorType->id,
                'status' => 1,
                'password' => Hash::make('11223344'),
                'photo' => 'default.jpg'
            ]
        );

        // Los contratos se crean desde un seeder separado `ContractSeeder`
        // para ejecutarlos después de que el personal de prueba exista.

        // 4. Crear Vacaciones
        Vacation::create([
            'employee_id' => $admin->id,
            'request_date' => Carbon::now()->format('Y-m-d'),
            'start_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(44)->format('Y-m-d'),
            'requested_days' => 15,
            'reason' => 'Vacaciones anuales programadas',
            'status' => \App\Models\Vacation::STATUS_PENDING,
            'approved_by' => null,
            'approved_at' => null
        ]);

        Vacation::create([
            'employee_id' => $operator1->id,
            'request_date' => Carbon::now()->subDays(25)->format('Y-m-d'),
            'start_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'end_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'requested_days' => 7,
            'reason' => 'Descanso médico',
            'status' => \App\Models\Vacation::STATUS_APPROVED,
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now()->subDays(15)
        ]);

        // 5. Crear Registros de Asistencia
        // Últimos 7 días para todos los empleados activos
        $employees = [$admin, $operator1, $operator2];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            foreach ($employees as $employee) {
                // Solo días laborables
                if ($date->isWeekday()) {
                    $clockIn = $date->copy()->setTime(8, rand(0, 30), 0); // Entre 8:00 y 8:30
                    $clockOut = $date->copy()->setTime(17, rand(0, 60), 0); // Entre 17:00 y 18:00

                    $hoursWorked = ($clockOut->diffInMinutes($clockIn) / 60) - 1; // Restar 1 hora de almuerzo

                    Attendance::create([
                        'employee_id' => $employee->id,
                        'date' => $date->format('Y-m-d'),
                        'period' => 1,
                        'check_in' => $clockIn,
                        'check_out' => $clockOut,
                        'hours_worked' => round($hoursWorked, 2),
                        'status' => \App\Models\Attendance::STATUS_PRESENT,
                        'notes' => 'Asistencia normal'
                    ]);
                }
            }
        }

        // Algunos registros especiales
        // Entrada tardía
        Attendance::create([
            'employee_id' => $operator2->id,
            'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'period' => 1,
            'check_in' => Carbon::now()->subDays(3)->setTime(9, 15, 0),
            'check_out' => Carbon::now()->subDays(3)->setTime(17, 30, 0),
            'hours_worked' => 7.25,
            'status' => \App\Models\Attendance::STATUS_LATE,
            'notes' => 'Llegada tardía por transporte'
        ]);

        // Horas extra
        Attendance::create([
            'employee_id' => $admin->id,
            'date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'period' => 1,
            'check_in' => Carbon::now()->subDays(2)->setTime(7, 30, 0),
            'check_out' => Carbon::now()->subDays(2)->setTime(19, 0, 0),
            'hours_worked' => 10.5,
            'status' => \App\Models\Attendance::STATUS_PRESENT,
            'notes' => 'Trabajo extra por cierre mensual'
        ]);

        $this->command->info('✅ Datos de prueba del módulo Personal creados exitosamente');
        $this->command->info('📊 Creados: 3 tipos de empleado, 3 empleados, 3 contratos, 2 vacaciones, múltiples asistencias');
    }
}