<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shifts')->insert([
            [
                'name' => 'Mañana',
                'description' => 'Turno de la mañana',
                'hora_in' => '06:00',
                'hora_out' => '14:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tarde',
                'description' => 'Turno de la tarde',
                'hora_in' => '14:00',
                'hora_out' => '22:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Noche',
                'description' => 'Turno de la noche',
                'hora_in' => '22:00',
                'hora_out' => '06:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
