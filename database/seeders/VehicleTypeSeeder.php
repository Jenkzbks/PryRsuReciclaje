<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Sedán',
                'description' => 'Vehículo de cuatro puertas para pasajeros.',
            ],
            [
                'name' => 'SUV',
                'description' => 'Vehículo utilitario deportivo.',
            ],
            [
                'name' => 'Pickup',
                'description' => 'Camioneta con caja de carga.',
            ],
            [
                'name' => 'Hatchback',
                'description' => 'Vehículo compacto con portón trasero.',
            ],
            [
                'name' => 'Camión',
                'description' => 'Vehículo de carga pesada.',
            ],
            [
                'name' => 'Minivan',
                'description' => 'Vehículo familiar con espacio amplio.',
            ],
        ];

        foreach ($types as $type) {
            VehicleType::create($type);
        }
    }
}
