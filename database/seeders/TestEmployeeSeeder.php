<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeType;
use Illuminate\Support\Facades\Hash;

class TestEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si existe al menos un tipo de empleado
        $employeeType = EmployeeType::first();
        
        if (!$employeeType) {
            // Crear un tipo de empleado básico
            $employeeType = EmployeeType::create([
                'name' => 'Empleado General',
                'description' => 'Empleado de prueba para el kiosco'
            ]);
        }

        // Crear empleado de prueba para el kiosco
        $testEmployee = Employee::updateOrCreate(
            ['dni' => '12345678'],
            [
                'names' => 'Juan',
                'lastnames' => 'Pérez García',
                'email' => 'juan.perez@empresa.com',
                'phone' => '987654321',
                'address' => 'Av. Test 123',
                'birthday' => '1990-01-01',
                'type_id' => $employeeType->id,
                'status' => 1,
                'password' => Hash::make('123456') // Contraseña de prueba
            ]
        );

        // Actualizar empleados existentes sin contraseña para usar su DNI como contraseña
        Employee::whereNull('password')->orWhere('password', '')->get()->each(function ($employee) {
            $employee->update([
                'password' => Hash::make($employee->dni)
            ]);
        });

        echo "Empleado de prueba creado:\n";
        echo "DNI: 12345678\n";
        echo "Contraseña: 123456\n\n";
        echo "Empleados existentes actualizados para usar su DNI como contraseña.\n";
    }
}