<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\District;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs dinámicamente para evitar problemas de sincronización
        $lima_dept = \App\Models\Department::where('name', 'Lima')->first();
        $callao_dept = \App\Models\Department::where('name', 'Callao')->first();
        $arequipa_dept = \App\Models\Department::where('name', 'Arequipa')->first();
        $trujillo_dept = \App\Models\Department::where('name', 'La Libertad')->first();
        $cusco_dept = \App\Models\Department::where('name', 'Cusco')->first();
        $piura_dept = \App\Models\Department::where('name', 'Piura')->first();
        $lambayeque_dept = \App\Models\Department::where('name', 'Lambayeque')->first();

        // Obtener provincias
        $lima_prov = \App\Models\Province::where('department_id', $lima_dept->id)->where('name', 'Lima')->first();
        $callao_prov = \App\Models\Province::where('department_id', $callao_dept->id)->where('name', 'Callao')->first();
        $canete_prov = \App\Models\Province::where('department_id', $lima_dept->id)->where('name', 'Cañete')->first();
        $arequipa_prov = \App\Models\Province::where('department_id', $arequipa_dept->id)->where('name', 'Arequipa')->first();
        $trujillo_prov = \App\Models\Province::where('department_id', $trujillo_dept->id)->where('name', 'Trujillo')->first();
        $cusco_prov = \App\Models\Province::where('department_id', $cusco_dept->id)->where('name', 'Cusco')->first();
        $piura_prov = \App\Models\Province::where('department_id', $piura_dept->id)->where('name', 'Piura')->first();
        $chiclayo_prov = \App\Models\Province::where('department_id', $lambayeque_dept->id)->where('name', 'Chiclayo')->first();

        $districts = [
            // LIMA METROPOLITANA - Provincia Lima
            ['name' => 'Lima', 'code' => '01', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Ancón', 'code' => '02', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Ate', 'code' => '03', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Barranco', 'code' => '04', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Breña', 'code' => '05', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Carabayllo', 'code' => '06', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Chaclacayo', 'code' => '07', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Chorrillos', 'code' => '08', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Cieneguilla', 'code' => '09', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Comas', 'code' => '10', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'El Agustino', 'code' => '11', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Independencia', 'code' => '12', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Jesús María', 'code' => '13', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'La Molina', 'code' => '14', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'La Victoria', 'code' => '15', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Lince', 'code' => '16', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Los Olivos', 'code' => '17', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Lurigancho', 'code' => '18', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Lurín', 'code' => '19', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Magdalena del Mar', 'code' => '20', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Miraflores', 'code' => '21', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Pachacamac', 'code' => '22', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Pucusana', 'code' => '23', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Pueblo Libre', 'code' => '24', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Puente Piedra', 'code' => '25', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Punta Hermosa', 'code' => '26', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Punta Negra', 'code' => '27', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Rímac', 'code' => '28', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'San Bartolo', 'code' => '29', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'San Borja', 'code' => '30', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'San Isidro', 'code' => '31', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'San Juan de Lurigancho', 'code' => '32', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'San Juan de Miraflores', 'code' => '33', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'San Luis', 'code' => '34', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'San Martín de Porres', 'code' => '35', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'San Miguel', 'code' => '36', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Santa Anita', 'code' => '37', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Santa María del Mar', 'code' => '38', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Santa Rosa', 'code' => '39', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Santiago de Surco', 'code' => '40', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Surquillo', 'code' => '41', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Villa El Salvador', 'code' => '42', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],
            ['name' => 'Villa María del Triunfo', 'code' => '43', 'department_id' => $lima_dept->id, 'province_id' => $lima_prov->id],

            // CALLAO - Provincia Callao
            ['name' => 'Callao', 'code' => '01', 'department_id' => $callao_dept->id, 'province_id' => $callao_prov->id],
            ['name' => 'Bellavista', 'code' => '02', 'department_id' => $callao_dept->id, 'province_id' => $callao_prov->id],
            ['name' => 'Carmen de la Legua Reynoso', 'code' => '03', 'department_id' => $callao_dept->id, 'province_id' => $callao_prov->id],
            ['name' => 'La Perla', 'code' => '04', 'department_id' => $callao_dept->id, 'province_id' => $callao_prov->id],
            ['name' => 'La Punta', 'code' => '05', 'department_id' => $callao_dept->id, 'province_id' => $callao_prov->id],
            ['name' => 'Ventanilla', 'code' => '06', 'department_id' => $callao_dept->id, 'province_id' => $callao_prov->id],
            ['name' => 'Mi Perú', 'code' => '07', 'department_id' => $callao_dept->id, 'province_id' => $callao_prov->id],

            // LIMA PROVINCIA - Provincia Cañete
            ['name' => 'San Vicente de Cañete', 'code' => '01', 'department_id' => $lima_dept->id, 'province_id' => $canete_prov->id],
            ['name' => 'Asia', 'code' => '02', 'department_id' => $lima_dept->id, 'province_id' => $canete_prov->id],
            ['name' => 'Calango', 'code' => '03', 'department_id' => $lima_dept->id, 'province_id' => $canete_prov->id],
            ['name' => 'Cerro Azul', 'code' => '04', 'department_id' => $lima_dept->id, 'province_id' => $canete_prov->id],
            ['name' => 'Chilca', 'code' => '05', 'department_id' => $lima_dept->id, 'province_id' => $canete_prov->id],
            ['name' => 'Imperial', 'code' => '06', 'department_id' => $lima_dept->id, 'province_id' => $canete_prov->id],
            ['name' => 'Lunahuaná', 'code' => '07', 'department_id' => $lima_dept->id, 'province_id' => $canete_prov->id],
            ['name' => 'Mala', 'code' => '08', 'department_id' => $lima_dept->id, 'province_id' => $canete_prov->id],

            // AREQUIPA - Provincia Arequipa
            ['name' => 'Arequipa', 'code' => '01', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Alto Selva Alegre', 'code' => '02', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Cayma', 'code' => '03', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Cerro Colorado', 'code' => '04', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Characato', 'code' => '05', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Jacobo Hunter', 'code' => '06', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Mariano Melgar', 'code' => '07', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Miraflores', 'code' => '08', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Paucarpata', 'code' => '09', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Sachaca', 'code' => '10', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Socabaya', 'code' => '11', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Tiabaya', 'code' => '12', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Yanahuara', 'code' => '13', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'Yura', 'code' => '14', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],
            ['name' => 'José Luis Bustamante y Rivero', 'code' => '15', 'department_id' => $arequipa_dept->id, 'province_id' => $arequipa_prov->id],

            // TRUJILLO - Provincia Trujillo
            ['name' => 'Trujillo', 'code' => '01', 'department_id' => $trujillo_dept->id, 'province_id' => $trujillo_prov->id],
            ['name' => 'El Porvenir', 'code' => '02', 'department_id' => $trujillo_dept->id, 'province_id' => $trujillo_prov->id],
            ['name' => 'Florencia de Mora', 'code' => '03', 'department_id' => $trujillo_dept->id, 'province_id' => $trujillo_prov->id],
            ['name' => 'Huanchaco', 'code' => '04', 'department_id' => $trujillo_dept->id, 'province_id' => $trujillo_prov->id],
            ['name' => 'La Esperanza', 'code' => '05', 'department_id' => $trujillo_dept->id, 'province_id' => $trujillo_prov->id],
            ['name' => 'Laredo', 'code' => '06', 'department_id' => $trujillo_dept->id, 'province_id' => $trujillo_prov->id],
            ['name' => 'Moche', 'code' => '07', 'department_id' => $trujillo_dept->id, 'province_id' => $trujillo_prov->id],
            ['name' => 'Salaverry', 'code' => '08', 'department_id' => $trujillo_dept->id, 'province_id' => $trujillo_prov->id],
            ['name' => 'Víctor Larco Herrera', 'code' => '09', 'department_id' => $trujillo_dept->id, 'province_id' => $trujillo_prov->id],

            // CUSCO - Provincia Cusco
            ['name' => 'Cusco', 'code' => '01', 'department_id' => $cusco_dept->id, 'province_id' => $cusco_prov->id],
            ['name' => 'San Jerónimo', 'code' => '02', 'department_id' => $cusco_dept->id, 'province_id' => $cusco_prov->id],
            ['name' => 'San Sebastián', 'code' => '03', 'department_id' => $cusco_dept->id, 'province_id' => $cusco_prov->id],
            ['name' => 'Santiago', 'code' => '04', 'department_id' => $cusco_dept->id, 'province_id' => $cusco_prov->id],
            ['name' => 'Wanchaq', 'code' => '05', 'department_id' => $cusco_dept->id, 'province_id' => $cusco_prov->id],

            // PIURA - Provincia Piura
            ['name' => 'Piura', 'code' => '01', 'department_id' => $piura_dept->id, 'province_id' => $piura_prov->id],
            ['name' => 'Castilla', 'code' => '02', 'department_id' => $piura_dept->id, 'province_id' => $piura_prov->id],
            ['name' => 'Catacaos', 'code' => '03', 'department_id' => $piura_dept->id, 'province_id' => $piura_prov->id],
            ['name' => 'La Arena', 'code' => '04', 'department_id' => $piura_dept->id, 'province_id' => $piura_prov->id],
            ['name' => 'Tambo Grande', 'code' => '05', 'department_id' => $piura_dept->id, 'province_id' => $piura_prov->id],

            // CHICLAYO - Provincia Chiclayo
            ['name' => 'Chiclayo', 'code' => '01', 'department_id' => $lambayeque_dept->id, 'province_id' => $chiclayo_prov->id],
            ['name' => 'Chongoyape', 'code' => '02', 'department_id' => $lambayeque_dept->id, 'province_id' => $chiclayo_prov->id],
            ['name' => 'Eten', 'code' => '03', 'department_id' => $lambayeque_dept->id, 'province_id' => $chiclayo_prov->id],
            ['name' => 'Eten Puerto', 'code' => '04', 'department_id' => $lambayeque_dept->id, 'province_id' => $chiclayo_prov->id],
            ['name' => 'José Leonardo Ortiz', 'code' => '05', 'department_id' => $lambayeque_dept->id, 'province_id' => $chiclayo_prov->id],
            ['name' => 'La Victoria', 'code' => '06', 'department_id' => $lambayeque_dept->id, 'province_id' => $chiclayo_prov->id],
            ['name' => 'Monsefú', 'code' => '07', 'department_id' => $lambayeque_dept->id, 'province_id' => $chiclayo_prov->id],
            ['name' => 'Pimentel', 'code' => '08', 'department_id' => $lambayeque_dept->id, 'province_id' => $chiclayo_prov->id],
            ['name' => 'Reque', 'code' => '09', 'department_id' => $lambayeque_dept->id, 'province_id' => $chiclayo_prov->id],
            ['name' => 'Santa Rosa', 'code' => '10', 'department_id' => $lambayeque_dept->id, 'province_id' => $chiclayo_prov->id],
        ];

        foreach ($districts as $district) {
            District::create($district);
        }
    }
}
