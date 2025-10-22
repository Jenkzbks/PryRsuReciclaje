<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeType;
use App\Models\Employee;
use App\Models\Contract;
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
            'descripcion' => 'Personal administrativo con acceso completo',
            'is_protected' => true
        ]);

        $operatorType = EmployeeType::create([
            'name' => 'Operador',
            'descripcion' => 'Personal operativo de reciclaje',
            'is_protected' => false
        ]);

        $supervisorType = EmployeeType::create([
            'name' => 'Supervisor',
            'descripcion' => 'Personal de supervisión',
            'is_protected' => false
        ]);

        // 2. Crear Empleados de Prueba
        $admin = Employee::create([
            'names' => 'Juan Carlos',
            'lastnames' => 'García López',
            'dni' => '12345678',
            'email' => 'admin@personal.test',
            'phone' => '987654321',
            'address' => 'Av. Principal 123, Lima',
            'birthday' => Carbon::now()->subYears(35)->format('Y-m-d'),
            'license' => 'A2B',
            'type_id' => $adminType->id,
            'status' => 1,
            'password' => Hash::make('12345678'),
            'photo' => 'default.jpg'
        ]);

        $operator1 = Employee::create([
            'names' => 'Pedro Luis',
            'lastnames' => 'Mendoza Silva',
            'dni' => '87654321',
            'email' => 'pedro@personal.test',
            'phone' => '976543210',
            'address' => 'Jr. Los Olivos 456, Lima',
            'birthday' => Carbon::now()->subYears(28)->format('Y-m-d'),
            'license' => 'A1',
            'type_id' => $operatorType->id,
            'status' => 1,
            'password' => Hash::make('87654321'),
            'photo' => 'default.jpg'
        ]);

        $operator2 = Employee::create([
            'names' => 'María Elena',
            'lastnames' => 'Rodríguez Vargas',
            'dni' => '11223344',
            'email' => 'maria@personal.test',
            'phone' => '965432109',
            'address' => 'Av. Los Pinos 789, Lima',
            'birthday' => Carbon::now()->subYears(32)->format('Y-m-d'),
            'license' => 'A1',
            'type_id' => $operatorType->id,
            'status' => 1,
            'password' => Hash::make('11223344'),
            'photo' => 'default.jpg'
        ]);

        // 3. Crear Contratos
        Contract::create([
            'employee_id' => $admin->id,
            'contract_type' => 'permanent',
            'start_date' => Carbon::now()->subMonths(24)->format('Y-m-d'),
            'end_date' => null,
            'salary' => 3500.00,
            'benefits' => 'Seguro médico familiar, bonificación anual',
            'is_active' => true
        ]);

        Contract::create([
            'employee_id' => $operator1->id,
            'contract_type' => 'temporary',
            'start_date' => Carbon::now()->subMonths(18)->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
            'salary' => 1800.00,
            'benefits' => 'Seguro médico básico',
            'is_active' => true
        ]);

        Contract::create([
            'employee_id' => $operator2->id,
            'contract_type' => 'permanent',
            'start_date' => Carbon::now()->subMonths(12)->format('Y-m-d'),
            'end_date' => null,
            'salary' => 1900.00,
            'benefits' => 'Seguro médico, transporte',
            'is_active' => true
        ]);

        // 4. Crear Vacaciones
        Vacation::create([
            'employee_id' => $admin->id,
            'start_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(44)->format('Y-m-d'),
            'total_days' => 15,
            'reason' => 'Vacaciones anuales programadas',
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null
        ]);

        Vacation::create([
            'employee_id' => $operator1->id,
            'start_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'end_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'total_days' => 7,
            'reason' => 'Descanso médico',
            'status' => 'approved',
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
                    
                    Attendance::create([
                        'employee_id' => $employee->id,
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut,
                        'break_time' => 60, // 1 hora de almuerzo
                        'total_hours' => $clockOut->diffInMinutes($clockIn) / 60 - 1, // Menos 1 hora de almuerzo
                        'overtime_hours' => 0,
                        'notes' => 'Asistencia normal'
                    ]);
                }
            }
        }

        // Algunos registros especiales
        // Entrada tardía
        Attendance::create([
            'employee_id' => $operator2->id,
            'clock_in' => Carbon::now()->subDays(3)->setTime(9, 15, 0),
            'clock_out' => Carbon::now()->subDays(3)->setTime(17, 30, 0),
            'break_time' => 60,
            'total_hours' => 7.25,
            'overtime_hours' => 0,
            'notes' => 'Llegada tardía por transporte'
        ]);

        // Horas extra
        Attendance::create([
            'employee_id' => $admin->id,
            'clock_in' => Carbon::now()->subDays(2)->setTime(7, 30, 0),
            'clock_out' => Carbon::now()->subDays(2)->setTime(19, 0, 0),
            'break_time' => 60,
            'total_hours' => 10.5,
            'overtime_hours' => 2.5,
            'notes' => 'Trabajo extra por cierre mensual'
        ]);

        $this->command->info('✅ Datos de prueba del módulo Personal creados exitosamente');
        $this->command->info('📊 Creados: 3 tipos de empleado, 3 empleados, 3 contratos, 2 vacaciones, múltiples asistencias');
    }
}