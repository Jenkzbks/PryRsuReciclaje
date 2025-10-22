<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeType;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SimplePersonnelSeeder extends Seeder
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
            'protected' => true,
            'active' => true,
            'level' => 1,
            'color' => '#dc3545',
            'icon' => 'fas fa-user-tie'
        ]);

        $operatorType = EmployeeType::create([
            'name' => 'Operador',
            'description' => 'Personal operativo de reciclaje',
            'protected' => false,
            'active' => true,
            'level' => 4,
            'color' => '#28a745',
            'icon' => 'fas fa-hard-hat'
        ]);

        // 2. Crear Empleados de Prueba
        Employee::create([
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

        Employee::create([
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

        Employee::create([
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

        $this->command->info('✅ Datos básicos del módulo Personal creados exitosamente');
        $this->command->info('📊 Creados: 2 tipos de empleado, 3 empleados');
    }
}