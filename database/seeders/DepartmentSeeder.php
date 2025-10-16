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
            ['name' => 'Amazonas', 'code' => '01', 'latitude' => -6.2339, 'longitude' => -77.8617, 'zoom_level' => 8],
            ['name' => 'Áncash', 'code' => '02', 'latitude' => -9.5277, 'longitude' => -77.5286, 'zoom_level' => 8],
            ['name' => 'Apurímac', 'code' => '03', 'latitude' => -14.0341, 'longitude' => -72.8788, 'zoom_level' => 8],
            ['name' => 'Arequipa', 'code' => '04', 'latitude' => -16.4090, 'longitude' => -71.5375, 'zoom_level' => 8],
            ['name' => 'Ayacucho', 'code' => '05', 'latitude' => -13.1631, 'longitude' => -74.2236, 'zoom_level' => 8],
            ['name' => 'Cajamarca', 'code' => '06', 'latitude' => -7.1561, 'longitude' => -78.5150, 'zoom_level' => 8],
            ['name' => 'Callao', 'code' => '07', 'latitude' => -12.0569, 'longitude' => -77.1189, 'zoom_level' => 11],
            ['name' => 'Cusco', 'code' => '08', 'latitude' => -13.5319, 'longitude' => -71.9675, 'zoom_level' => 8],
            ['name' => 'Huancavelica', 'code' => '09', 'latitude' => -12.7869, 'longitude' => -74.9731, 'zoom_level' => 8],
            ['name' => 'Huánuco', 'code' => '10', 'latitude' => -9.9306, 'longitude' => -76.2422, 'zoom_level' => 8],
            ['name' => 'Ica', 'code' => '11', 'latitude' => -14.0678, 'longitude' => -75.7286, 'zoom_level' => 9],
            ['name' => 'Junín', 'code' => '12', 'latitude' => -11.1581, 'longitude' => -75.9914, 'zoom_level' => 8],
            ['name' => 'La Libertad', 'code' => '13', 'latitude' => -8.1116, 'longitude' => -79.0292, 'zoom_level' => 8],
            ['name' => 'Lambayeque', 'code' => '14', 'latitude' => -6.7011, 'longitude' => -79.9061, 'zoom_level' => 9],
            ['name' => 'Lima', 'code' => '15', 'latitude' => -12.0464, 'longitude' => -77.0428, 'zoom_level' => 9],
            ['name' => 'Loreto', 'code' => '16', 'latitude' => -3.7437, 'longitude' => -73.2516, 'zoom_level' => 7],
            ['name' => 'Madre de Dios', 'code' => '17', 'latitude' => -12.5934, 'longitude' => -69.1890, 'zoom_level' => 8],
            ['name' => 'Moquegua', 'code' => '18', 'latitude' => -17.1934, 'longitude' => -70.9356, 'zoom_level' => 9],
            ['name' => 'Pasco', 'code' => '19', 'latitude' => -10.6926, 'longitude' => -76.2661, 'zoom_level' => 8],
            ['name' => 'Piura', 'code' => '20', 'latitude' => -5.1945, 'longitude' => -80.6328, 'zoom_level' => 8],
            ['name' => 'Puno', 'code' => '21', 'latitude' => -15.8402, 'longitude' => -70.0219, 'zoom_level' => 8],
            ['name' => 'San Martín', 'code' => '22', 'latitude' => -6.4869, 'longitude' => -76.3653, 'zoom_level' => 8],
            ['name' => 'Tacna', 'code' => '23', 'latitude' => -18.0131, 'longitude' => -70.2536, 'zoom_level' => 9],
            ['name' => 'Tumbes', 'code' => '24', 'latitude' => -3.5669, 'longitude' => -80.4517, 'zoom_level' => 9],
            ['name' => 'Ucayali', 'code' => '25', 'latitude' => -8.3791, 'longitude' => -74.5539, 'zoom_level' => 7],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
