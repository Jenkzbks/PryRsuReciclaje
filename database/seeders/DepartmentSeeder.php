<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Amazonas', 'code' => '01'],
            ['name' => 'Áncash', 'code' => '02'],
            ['name' => 'Apurímac', 'code' => '03'],
            ['name' => 'Arequipa', 'code' => '04'],
            ['name' => 'Ayacucho', 'code' => '05'],
            ['name' => 'Cajamarca', 'code' => '06'],
            ['name' => 'Callao', 'code' => '07'],
            ['name' => 'Cusco', 'code' => '08'],
            ['name' => 'Huancavelica', 'code' => '09'],
            ['name' => 'Huánuco', 'code' => '10'],
            ['name' => 'Ica', 'code' => '11'],
            ['name' => 'Junín', 'code' => '12'],
            ['name' => 'La Libertad', 'code' => '13'],
            ['name' => 'Lambayeque', 'code' => '14'],
            ['name' => 'Lima', 'code' => '15'],
            ['name' => 'Loreto', 'code' => '16'],
            ['name' => 'Madre de Dios', 'code' => '17'],
            ['name' => 'Moquegua', 'code' => '18'],
            ['name' => 'Pasco', 'code' => '19'],
            ['name' => 'Piura', 'code' => '20'],
            ['name' => 'Puno', 'code' => '21'],
            ['name' => 'San Martín', 'code' => '22'],
            ['name' => 'Tacna', 'code' => '23'],
            ['name' => 'Tumbes', 'code' => '24'],
            ['name' => 'Ucayali', 'code' => '25'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
