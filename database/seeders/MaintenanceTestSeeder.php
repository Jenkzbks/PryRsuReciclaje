<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maintenance;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\Employee;

class MaintenanceTestSeeder extends Seeder
{
    public function run()
    {
        // Obtener algunos vehículos y empleados (si existen)
        $vehicles = Vehicle::take(3)->get();
        $employees = Employee::take(3)->get();

        if ($vehicles->count() > 0) {
            // Crear mantenimientos de prueba
            $maintenances = [
                [
                    'name' => 'Mantenimiento Preventivo Vehículo 1',
                    'vehicle_id' => $vehicles->first()->id,
                    'maintenance_type' => 'Preventivo',
                    'description' => 'Mantenimiento preventivo programado - Cambio de aceite y filtros',
                    'scheduled_date' => now()->addDays(5),
                    'start_date' => now()->addDays(5),
                    'end_date' => now()->addDays(5),
                    'status' => 'programado',
                ],
                [
                    'name' => 'Reparación Sistema de Frenos',
                    'vehicle_id' => $vehicles->count() > 1 ? $vehicles[1]->id : $vehicles->first()->id,
                    'maintenance_type' => 'Correctivo',
                    'description' => 'Reparación de sistema de frenos',
                    'scheduled_date' => now()->addDays(10),
                    'start_date' => now()->addDays(10),
                    'end_date' => now()->addDays(10),
                    'status' => 'programado',
                ],
                [
                    'name' => 'Inspección Técnica Anual',
                    'vehicle_id' => $vehicles->count() > 2 ? $vehicles[2]->id : $vehicles->first()->id,
                    'maintenance_type' => 'Inspección',
                    'description' => 'Inspección técnica vehicular anual',
                    'scheduled_date' => now()->subDays(5),
                    'start_date' => now()->subDays(5),
                    'end_date' => now()->subDays(2),
                    'completed_date' => now()->subDays(2),
                    'status' => 'completado',
                ]
            ];

            foreach ($maintenances as $maintenanceData) {
                $maintenance = Maintenance::create($maintenanceData);

                // Crear horarios de mantenimiento
                MaintenanceSchedule::create([
                    'maintenance_id' => $maintenance->id,
                    'start_date' => $maintenance->scheduled_date,
                    'end_date' => $maintenance->scheduled_date->copy()->addHours(4),
                    'description' => 'Horario asignado para ' . $maintenance->description,
                ]);

                // Crear registros de actividades para mantenimientos completados
                if ($maintenance->status === 'completado') {
                    MaintenanceRecord::create([
                        'maintenance_id' => $maintenance->id,
                        'employee_id' => $employees->count() > 0 ? $employees->first()->id : null,
                        'activity_description' => 'Actividad realizada: ' . $maintenance->description,
                        'activity_date' => $maintenance->completed_date ?? $maintenance->scheduled_date,
                        'notes' => 'Mantenimiento completado satisfactoriamente',
                    ]);
                }
            }

            $this->command->info('Se han creado datos de prueba para el módulo de mantenimiento');
        } else {
            $this->command->warn('No se encontraron vehículos en la base de datos. Creando datos básicos...');
            
            // Si no hay vehículos, mostrar mensaje informativo
            $this->command->info('Para usar el módulo de mantenimiento necesitas:');
            $this->command->info('1. Vehículos registrados en el sistema');
            $this->command->info('2. Empleados registrados en el sistema');
        }
    }
}