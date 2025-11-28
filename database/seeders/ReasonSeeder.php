<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        DB::table('reasons')->insert([
            [ 'name' => 'Vacaciones programadas', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Descanso médico', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Permiso por asuntos personales', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Cambio de turno por rotación', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Mantenimiento preventivo de vehículo', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Falla mecánica de vehículo', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Ausencia de conductor', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Ausencia de ayudante', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Capacitación obligatoria', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Sanción disciplinaria', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Reemplazo por vacaciones de otro', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Reasignación por alta demanda', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Evento extraordinario', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Cobertura de ruta especial', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
            [ 'name' => 'Otro motivo', 'active' => 1, 'created_at' => $now, 'updated_at' => $now ],
        ]);
    }
}
