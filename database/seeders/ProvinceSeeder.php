<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            // Amazonas (ID: 1)
            ['name' => 'Chachapoyas', 'code' => '01', 'department_id' => 1],
            ['name' => 'Bagua', 'code' => '02', 'department_id' => 1],
            ['name' => 'Bongará', 'code' => '03', 'department_id' => 1],
            ['name' => 'Condorcanqui', 'code' => '04', 'department_id' => 1],
            ['name' => 'Luya', 'code' => '05', 'department_id' => 1],
            ['name' => 'Rodríguez de Mendoza', 'code' => '06', 'department_id' => 1],
            ['name' => 'Utcubamba', 'code' => '07', 'department_id' => 1],

            // Áncash (ID: 2)
            ['name' => 'Huaraz', 'code' => '01', 'department_id' => 2],
            ['name' => 'Aija', 'code' => '02', 'department_id' => 2],
            ['name' => 'Antonio Raymondi', 'code' => '03', 'department_id' => 2],
            ['name' => 'Asunción', 'code' => '04', 'department_id' => 2],
            ['name' => 'Bolognesi', 'code' => '05', 'department_id' => 2],
            ['name' => 'Carhuaz', 'code' => '06', 'department_id' => 2],
            ['name' => 'Carlos Fermín Fitzcarrald', 'code' => '07', 'department_id' => 2],
            ['name' => 'Casma', 'code' => '08', 'department_id' => 2],
            ['name' => 'Corongo', 'code' => '09', 'department_id' => 2],
            ['name' => 'Huari', 'code' => '10', 'department_id' => 2],
            ['name' => 'Huarmey', 'code' => '11', 'department_id' => 2],
            ['name' => 'Huaylas', 'code' => '12', 'department_id' => 2],
            ['name' => 'Mariscal Luzuriaga', 'code' => '13', 'department_id' => 2],
            ['name' => 'Ocros', 'code' => '14', 'department_id' => 2],
            ['name' => 'Pallasca', 'code' => '15', 'department_id' => 2],
            ['name' => 'Pomabamba', 'code' => '16', 'department_id' => 2],
            ['name' => 'Recuay', 'code' => '17', 'department_id' => 2],
            ['name' => 'Santa', 'code' => '18', 'department_id' => 2],
            ['name' => 'Sihuas', 'code' => '19', 'department_id' => 2],
            ['name' => 'Yungay', 'code' => '20', 'department_id' => 2],

            // Apurímac (ID: 3)
            ['name' => 'Abancay', 'code' => '01', 'department_id' => 3],
            ['name' => 'Andahuaylas', 'code' => '02', 'department_id' => 3],
            ['name' => 'Antabamba', 'code' => '03', 'department_id' => 3],
            ['name' => 'Aymaraes', 'code' => '04', 'department_id' => 3],
            ['name' => 'Cotabambas', 'code' => '05', 'department_id' => 3],
            ['name' => 'Chincheros', 'code' => '06', 'department_id' => 3],
            ['name' => 'Grau', 'code' => '07', 'department_id' => 3],

            // Arequipa (ID: 4)
            ['name' => 'Arequipa', 'code' => '01', 'department_id' => 4],
            ['name' => 'Camaná', 'code' => '02', 'department_id' => 4],
            ['name' => 'Caravelí', 'code' => '03', 'department_id' => 4],
            ['name' => 'Castilla', 'code' => '04', 'department_id' => 4],
            ['name' => 'Caylloma', 'code' => '05', 'department_id' => 4],
            ['name' => 'Condesuyos', 'code' => '06', 'department_id' => 4],
            ['name' => 'Islay', 'code' => '07', 'department_id' => 4],
            ['name' => 'La Unión', 'code' => '08', 'department_id' => 4],

            // Ayacucho (ID: 5)
            ['name' => 'Huamanga', 'code' => '01', 'department_id' => 5],
            ['name' => 'Cangallo', 'code' => '02', 'department_id' => 5],
            ['name' => 'Huanca Sancos', 'code' => '03', 'department_id' => 5],
            ['name' => 'Huanta', 'code' => '04', 'department_id' => 5],
            ['name' => 'La Mar', 'code' => '05', 'department_id' => 5],
            ['name' => 'Lucanas', 'code' => '06', 'department_id' => 5],
            ['name' => 'Parinacochas', 'code' => '07', 'department_id' => 5],
            ['name' => 'Paucar del Sara Sara', 'code' => '08', 'department_id' => 5],
            ['name' => 'Sucre', 'code' => '09', 'department_id' => 5],
            ['name' => 'Víctor Fajardo', 'code' => '10', 'department_id' => 5],
            ['name' => 'Vilcas Huamán', 'code' => '11', 'department_id' => 5],

            // Cajamarca (ID: 6)
            ['name' => 'Cajamarca', 'code' => '01', 'department_id' => 6],
            ['name' => 'Cajabamba', 'code' => '02', 'department_id' => 6],
            ['name' => 'Celendín', 'code' => '03', 'department_id' => 6],
            ['name' => 'Chota', 'code' => '04', 'department_id' => 6],
            ['name' => 'Contumazá', 'code' => '05', 'department_id' => 6],
            ['name' => 'Cutervo', 'code' => '06', 'department_id' => 6],
            ['name' => 'Hualgayoc', 'code' => '07', 'department_id' => 6],
            ['name' => 'Jaén', 'code' => '08', 'department_id' => 6],
            ['name' => 'San Ignacio', 'code' => '09', 'department_id' => 6],
            ['name' => 'San Marcos', 'code' => '10', 'department_id' => 6],
            ['name' => 'San Miguel', 'code' => '11', 'department_id' => 6],
            ['name' => 'San Pablo', 'code' => '12', 'department_id' => 6],
            ['name' => 'Santa Cruz', 'code' => '13', 'department_id' => 6],

            // Callao (ID: 7)
            ['name' => 'Callao', 'code' => '01', 'department_id' => 7],

            // Cusco (ID: 8)
            ['name' => 'Cusco', 'code' => '01', 'department_id' => 8],
            ['name' => 'Acomayo', 'code' => '02', 'department_id' => 8],
            ['name' => 'Anta', 'code' => '03', 'department_id' => 8],
            ['name' => 'Calca', 'code' => '04', 'department_id' => 8],
            ['name' => 'Canas', 'code' => '05', 'department_id' => 8],
            ['name' => 'Canchis', 'code' => '06', 'department_id' => 8],
            ['name' => 'Chumbivilcas', 'code' => '07', 'department_id' => 8],
            ['name' => 'Espinar', 'code' => '08', 'department_id' => 8],
            ['name' => 'La Convención', 'code' => '09', 'department_id' => 8],
            ['name' => 'Paruro', 'code' => '10', 'department_id' => 8],
            ['name' => 'Paucartambo', 'code' => '11', 'department_id' => 8],
            ['name' => 'Quispicanchi', 'code' => '12', 'department_id' => 8],
            ['name' => 'Urubamba', 'code' => '13', 'department_id' => 8],

            // La Libertad (ID: 13)
            ['name' => 'Trujillo', 'code' => '01', 'department_id' => 13],
            ['name' => 'Ascope', 'code' => '02', 'department_id' => 13],
            ['name' => 'Bolívar', 'code' => '03', 'department_id' => 13],
            ['name' => 'Chepén', 'code' => '04', 'department_id' => 13],
            ['name' => 'Julcán', 'code' => '05', 'department_id' => 13],
            ['name' => 'Otuzco', 'code' => '06', 'department_id' => 13],
            ['name' => 'Pacasmayo', 'code' => '07', 'department_id' => 13],
            ['name' => 'Pataz', 'code' => '08', 'department_id' => 13],
            ['name' => 'Sánchez Carrión', 'code' => '09', 'department_id' => 13],
            ['name' => 'Santiago de Chuco', 'code' => '10', 'department_id' => 13],
            ['name' => 'Gran Chimú', 'code' => '11', 'department_id' => 13],
            ['name' => 'Virú', 'code' => '12', 'department_id' => 13],

            // Lambayeque (ID: 14)
            ['name' => 'Chiclayo', 'code' => '01', 'department_id' => 14],
            ['name' => 'Ferreñafe', 'code' => '02', 'department_id' => 14],
            ['name' => 'Lambayeque', 'code' => '03', 'department_id' => 14],

            // Lima (ID: 15)
            ['name' => 'Lima', 'code' => '01', 'department_id' => 15],
            ['name' => 'Barranca', 'code' => '02', 'department_id' => 15],
            ['name' => 'Cajatambo', 'code' => '03', 'department_id' => 15],
            ['name' => 'Canta', 'code' => '04', 'department_id' => 15],
            ['name' => 'Cañete', 'code' => '05', 'department_id' => 15],
            ['name' => 'Huaral', 'code' => '06', 'department_id' => 15],
            ['name' => 'Huarochirí', 'code' => '07', 'department_id' => 15],
            ['name' => 'Huaura', 'code' => '08', 'department_id' => 15],
            ['name' => 'Oyón', 'code' => '09', 'department_id' => 15],
            ['name' => 'Yauyos', 'code' => '10', 'department_id' => 15],

            // Loreto (ID: 16)
            ['name' => 'Maynas', 'code' => '01', 'department_id' => 16],
            ['name' => 'Alto Amazonas', 'code' => '02', 'department_id' => 16],
            ['name' => 'Loreto', 'code' => '03', 'department_id' => 16],
            ['name' => 'Mariscal Ramón Castilla', 'code' => '04', 'department_id' => 16],
            ['name' => 'Requena', 'code' => '05', 'department_id' => 16],
            ['name' => 'Ucayali', 'code' => '06', 'department_id' => 16],
            ['name' => 'Datem del Marañón', 'code' => '07', 'department_id' => 16],
            ['name' => 'Putumayo', 'code' => '08', 'department_id' => 16],

            // Piura (ID: 20)
            // Piura (ID: 20)
            ['name' => 'Piura', 'code' => '01', 'department_id' => 20],
            ['name' => 'Ayabaca', 'code' => '02', 'department_id' => 20],
            ['name' => 'Huancabamba', 'code' => '03', 'department_id' => 20],
            ['name' => 'Morropón', 'code' => '04', 'department_id' => 20],
            ['name' => 'Paita', 'code' => '05', 'department_id' => 20],
            ['name' => 'Sullana', 'code' => '06', 'department_id' => 20],
            ['name' => 'Talara', 'code' => '07', 'department_id' => 20],
            ['name' => 'Sechura', 'code' => '08', 'department_id' => 20],

            // Puno (ID: 21)
            ['name' => 'Puno', 'code' => '01', 'department_id' => 21],
            ['name' => 'Azángaro', 'code' => '02', 'department_id' => 21],
            ['name' => 'Carabaya', 'code' => '03', 'department_id' => 21],
            ['name' => 'Chucuito', 'code' => '04', 'department_id' => 21],
            ['name' => 'El Collao', 'code' => '05', 'department_id' => 21],
            ['name' => 'Huancané', 'code' => '06', 'department_id' => 21],
            ['name' => 'Lampa', 'code' => '07', 'department_id' => 21],
            ['name' => 'Melgar', 'code' => '08', 'department_id' => 21],
            ['name' => 'Moho', 'code' => '09', 'department_id' => 21],
            ['name' => 'San Antonio de Putina', 'code' => '10', 'department_id' => 21],
            ['name' => 'San Román', 'code' => '11', 'department_id' => 21],
            ['name' => 'Sandia', 'code' => '12', 'department_id' => 21],
            ['name' => 'Yunguyo', 'code' => '13', 'department_id' => 21],

            // San Martín (ID: 22)
            ['name' => 'Moyobamba', 'code' => '01', 'department_id' => 22],
            ['name' => 'Bellavista', 'code' => '02', 'department_id' => 22],
            ['name' => 'El Dorado', 'code' => '03', 'department_id' => 22],
            ['name' => 'Huallaga', 'code' => '04', 'department_id' => 22],
            ['name' => 'Lamas', 'code' => '05', 'department_id' => 22],
            ['name' => 'Mariscal Cáceres', 'code' => '06', 'department_id' => 22],
            ['name' => 'Picota', 'code' => '07', 'department_id' => 22],
            ['name' => 'Rioja', 'code' => '08', 'department_id' => 22],
            ['name' => 'San Martín', 'code' => '09', 'department_id' => 22],
            ['name' => 'Tocache', 'code' => '10', 'department_id' => 22],

            // Tacna (ID: 23)
            ['name' => 'Tacna', 'code' => '01', 'department_id' => 23],
            ['name' => 'Candarave', 'code' => '02', 'department_id' => 23],
            ['name' => 'Jorge Basadre', 'code' => '03', 'department_id' => 23],
            ['name' => 'Tarata', 'code' => '04', 'department_id' => 23],

            // Tumbes (ID: 24)
            ['name' => 'Tumbes', 'code' => '01', 'department_id' => 24],
            ['name' => 'Contralmirante Villar', 'code' => '02', 'department_id' => 24],
            ['name' => 'Zarumilla', 'code' => '03', 'department_id' => 24],

            // Ucayali (ID: 25)
            ['name' => 'Coronel Portillo', 'code' => '01', 'department_id' => 25],
            ['name' => 'Atalaya', 'code' => '02', 'department_id' => 25],
            ['name' => 'Padre Abad', 'code' => '03', 'department_id' => 25],
            ['name' => 'Purús', 'code' => '04', 'department_id' => 25],
        ];

        foreach ($provinces as $province) {
            Province::create($province);
        }
    }
}
