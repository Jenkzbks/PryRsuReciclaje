
# 🗺️ **INSERTS SQL COMPLETOS - GESTIÓN DE ZONAS**

## 📋 **RESUMEN**
Scripts SQL actualizados y funcionales para el sistema de Gestión de Zonas con datos geográficos completos del Perú.

---

## 🏛️ **1. DEPARTAMENTOS (25 TOTAL)**

### **SQL INSERT Departments**

```sql
INSERT INTO departments (name, code, latitude, longitude, zoom_level, created_at, updated_at) VALUES
('Amazonas', '01', -6.2339, -77.8617, 8, NOW(), NOW()),
('Áncash', '02', -9.5277, -77.5286, 8, NOW(), NOW()),
('Apurímac', '03', -14.0341, -72.8788, 8, NOW(), NOW()),
('Arequipa', '04', -16.4090, -71.5375, 8, NOW(), NOW()),
('Ayacucho', '05', -13.1631, -74.2236, 8, NOW(), NOW()),
('Cajamarca', '06', -7.1561, -78.5150, 8, NOW(), NOW()),
('Callao', '07', -12.0569, -77.1189, 11, NOW(), NOW()),
('Cusco', '08', -13.5319, -71.9675, 8, NOW(), NOW()),
('Huancavelica', '09', -12.7869, -74.9731, 8, NOW(), NOW()),
('Huánuco', '10', -9.9306, -76.2422, 8, NOW(), NOW()),
('Ica', '11', -14.0678, -75.7286, 9, NOW(), NOW()),
('Junín', '12', -11.1581, -75.9914, 8, NOW(), NOW()),
('La Libertad', '13', -8.1116, -79.0292, 8, NOW(), NOW()),
('Lambayeque', '14', -6.7011, -79.9061, 9, NOW(), NOW()),
('Lima', '15', -12.0464, -77.0428, 9, NOW(), NOW()),
('Loreto', '16', -3.7437, -73.2516, 7, NOW(), NOW()),
('Madre de Dios', '17', -12.5934, -69.1890, 8, NOW(), NOW()),
('Moquegua', '18', -17.1934, -70.9356, 9, NOW(), NOW()),
('Pasco', '19', -10.6926, -76.2661, 8, NOW(), NOW()),
('Piura', '20', -5.1945, -80.6328, 8, NOW(), NOW()),
('Puno', '21', -15.8402, -70.0219, 8, NOW(), NOW()),
('San Martín', '22', -6.4869, -76.3653, 8, NOW(), NOW()),
('Tacna', '23', -18.0131, -70.2536, 9, NOW(), NOW()),
('Tumbes', '24', -3.5669, -80.4517, 9, NOW(), NOW()),
('Ucayali', '25', -8.3791, -74.5539, 7, NOW(), NOW());
```

---

## 🌆 **2. PROVINCIAS POR DEPARTAMENTO**

### **AMAZONAS (7 provincias)**
```sql
INSERT INTO provinces (name, code, department_id, created_at, updated_at) VALUES
('Chachapoyas', '01', 1, NOW(), NOW()),
('Bagua', '02', 1, NOW(), NOW()),
('Bongará', '03', 1, NOW(), NOW()),
('Condorcanqui', '04', 1, NOW(), NOW()),
('Luya', '05', 1, NOW(), NOW()),
('Rodríguez de Mendoza', '06', 1, NOW(), NOW()),
('Utcubamba', '07', 1, NOW(), NOW());
```

### **ÁNCASH (20 provincias)**
```sql
INSERT INTO provinces (name, code, department_id, created_at, updated_at) VALUES
('Huaraz', '01', 2, NOW(), NOW()),
('Aija', '02', 2, NOW(), NOW()),
('Antonio Raymondi', '03', 2, NOW(), NOW()),
('Asunción', '04', 2, NOW(), NOW()),
('Bolognesi', '05', 2, NOW(), NOW()),
('Carhuaz', '06', 2, NOW(), NOW()),
('Carlos Fermín Fitzcarrald', '07', 2, NOW(), NOW()),
('Casma', '08', 2, NOW(), NOW()),
('Corongo', '09', 2, NOW(), NOW()),
('Huari', '10', 2, NOW(), NOW()),
('Huarmey', '11', 2, NOW(), NOW()),
('Huaylas', '12', 2, NOW(), NOW()),
('Mariscal Luzuriaga', '13', 2, NOW(), NOW()),
('Ocros', '14', 2, NOW(), NOW()),
('Pallasca', '15', 2, NOW(), NOW()),
('Pomabamba', '16', 2, NOW(), NOW()),
('Recuay', '17', 2, NOW(), NOW()),
('Santa', '18', 2, NOW(), NOW()),
('Sihuas', '19', 2, NOW(), NOW()),
('Yungay', '20', 2, NOW(), NOW());
```

### **AREQUIPA (8 provincias)**
```sql
INSERT INTO provinces (name, code, department_id, created_at, updated_at) VALUES
('Arequipa', '01', 4, NOW(), NOW()),
('Camaná', '02', 4, NOW(), NOW()),
('Caravelí', '03', 4, NOW(), NOW()),
('Castilla', '04', 4, NOW(), NOW()),
('Caylloma', '05', 4, NOW(), NOW()),
('Condesuyos', '06', 4, NOW(), NOW()),
('Islay', '07', 4, NOW(), NOW()),
('La Unión', '08', 4, NOW(), NOW());
```

### **CUSCO (13 provincias)**
```sql
INSERT INTO provinces (name, code, department_id, created_at, updated_at) VALUES
('Cusco', '01', 8, NOW(), NOW()),
('Acomayo', '02', 8, NOW(), NOW()),
('Anta', '03', 8, NOW(), NOW()),
('Calca', '04', 8, NOW(), NOW()),
('Canas', '05', 8, NOW(), NOW()),
('Canchis', '06', 8, NOW(), NOW()),
('Chumbivilcas', '07', 8, NOW(), NOW()),
('Espinar', '08', 8, NOW(), NOW()),
('La Convención', '09', 8, NOW(), NOW()),
('Paruro', '10', 8, NOW(), NOW()),
('Paucartambo', '11', 8, NOW(), NOW()),
('Quispicanchi', '12', 8, NOW(), NOW()),
('Urubamba', '13', 8, NOW(), NOW());
```

### **LA LIBERTAD (12 provincias)**
```sql
INSERT INTO provinces (name, code, department_id, created_at, updated_at) VALUES
('Trujillo', '01', 13, NOW(), NOW()),
('Ascope', '02', 13, NOW(), NOW()),
('Bolívar', '03', 13, NOW(), NOW()),
('Chepén', '04', 13, NOW(), NOW()),
('Julcán', '05', 13, NOW(), NOW()),
('Otuzco', '06', 13, NOW(), NOW()),
('Pacasmayo', '07', 13, NOW(), NOW()),
('Pataz', '08', 13, NOW(), NOW()),
('Sánchez Carrión', '09', 13, NOW(), NOW()),
('Santiago de Chuco', '10', 13, NOW(), NOW()),
('Gran Chimú', '11', 13, NOW(), NOW()),
('Virú', '12', 13, NOW(), NOW());
```

### **LAMBAYEQUE (3 provincias)**
```sql
INSERT INTO provinces (name, code, department_id, created_at, updated_at) VALUES
('Chiclayo', '01', 14, NOW(), NOW()),
('Ferreñafe', '02', 14, NOW(), NOW()),
('Lambayeque', '03', 14, NOW(), NOW());
```

### **LIMA (10 provincias)**
```sql
INSERT INTO provinces (name, code, department_id, created_at, updated_at) VALUES
('Lima', '01', 15, NOW(), NOW()),
('Barranca', '02', 15, NOW(), NOW()),
('Cajatambo', '03', 15, NOW(), NOW()),
('Canta', '04', 15, NOW(), NOW()),
('Cañete', '05', 15, NOW(), NOW()),
('Huaral', '06', 15, NOW(), NOW()),
('Huarochirí', '07', 15, NOW(), NOW()),
('Huaura', '08', 15, NOW(), NOW()),
('Oyón', '09', 15, NOW(), NOW()),
('Yauyos', '10', 15, NOW(), NOW());
```

### **PIURA (8 provincias)**
```sql
INSERT INTO provinces (name, code, department_id, created_at, updated_at) VALUES
('Piura', '01', 20, NOW(), NOW()),
('Ayabaca', '02', 20, NOW(), NOW()),
('Huancabamba', '03', 20, NOW(), NOW()),
('Morropón', '04', 20, NOW(), NOW()),
('Paita', '05', 20, NOW(), NOW()),
('Sullana', '06', 20, NOW(), NOW()),
('Talara', '07', 20, NOW(), NOW()),
('Sechura', '08', 20, NOW(), NOW());
```

---

## 🏘️ **3. DISTRITOS PRINCIPALES**

### **LIMA METROPOLITANA (43 distritos)**
```sql
INSERT INTO districts (name, code, department_id, province_id, created_at, updated_at) VALUES
-- Provincia de Lima (ID: 1)
('Lima', '01', 15, 1, NOW(), NOW()),
('Ancón', '02', 15, 1, NOW(), NOW()),
('Ate', '03', 15, 1, NOW(), NOW()),
('Barranco', '04', 15, 1, NOW(), NOW()),
('Breña', '05', 15, 1, NOW(), NOW()),
('Carabayllo', '06', 15, 1, NOW(), NOW()),
('Chaclacayo', '07', 15, 1, NOW(), NOW()),
('Chorrillos', '08', 15, 1, NOW(), NOW()),
('Cieneguilla', '09', 15, 1, NOW(), NOW()),
('Comas', '10', 15, 1, NOW(), NOW()),
('El Agustino', '11', 15, 1, NOW(), NOW()),
('Independencia', '12', 15, 1, NOW(), NOW()),
('Jesús María', '13', 15, 1, NOW(), NOW()),
('La Molina', '14', 15, 1, NOW(), NOW()),
('La Victoria', '15', 15, 1, NOW(), NOW()),
('Lince', '16', 15, 1, NOW(), NOW()),
('Los Olivos', '17', 15, 1, NOW(), NOW()),
('Lurigancho', '18', 15, 1, NOW(), NOW()),
('Lurín', '19', 15, 1, NOW(), NOW()),
('Magdalena del Mar', '20', 15, 1, NOW(), NOW()),
('Miraflores', '21', 15, 1, NOW(), NOW()),
('Pachacamac', '22', 15, 1, NOW(), NOW()),
('Pucusana', '23', 15, 1, NOW(), NOW()),
('Pueblo Libre', '24', 15, 1, NOW(), NOW()),
('Puente Piedra', '25', 15, 1, NOW(), NOW()),
('Punta Hermosa', '26', 15, 1, NOW(), NOW()),
('Punta Negra', '27', 15, 1, NOW(), NOW()),
('Rímac', '28', 15, 1, NOW(), NOW()),
('San Bartolo', '29', 15, 1, NOW(), NOW()),
('San Borja', '30', 15, 1, NOW(), NOW()),
('San Isidro', '31', 15, 1, NOW(), NOW()),
('San Juan de Lurigancho', '32', 15, 1, NOW(), NOW()),
('San Juan de Miraflores', '33', 15, 1, NOW(), NOW()),
('San Luis', '34', 15, 1, NOW(), NOW()),
('San Martín de Porres', '35', 15, 1, NOW(), NOW()),
('San Miguel', '36', 15, 1, NOW(), NOW()),
('Santa Anita', '37', 15, 1, NOW(), NOW()),
('Santa María del Mar', '38', 15, 1, NOW(), NOW()),
('Santa Rosa', '39', 15, 1, NOW(), NOW()),
('Santiago de Surco', '40', 15, 1, NOW(), NOW()),
('Surquillo', '41', 15, 1, NOW(), NOW()),
('Villa El Salvador', '42', 15, 1, NOW(), NOW()),
('Villa María del Triunfo', '43', 15, 1, NOW(), NOW());
```

### **CALLAO (7 distritos)**
```sql
INSERT INTO districts (name, code, department_id, province_id, created_at, updated_at) VALUES
-- Provincia Constitucional del Callao
('Callao', '01', 7, 1, NOW(), NOW()),
('Bellavista', '02', 7, 1, NOW(), NOW()),
('Carmen de la Legua Reynoso', '03', 7, 1, NOW(), NOW()),
('La Perla', '04', 7, 1, NOW(), NOW()),
('La Punta', '05', 7, 1, NOW(), NOW()),
('Ventanilla', '06', 7, 1, NOW(), NOW()),
('Mi Perú', '07', 7, 1, NOW(), NOW());
```

### **AREQUIPA (29 distritos)**
```sql
INSERT INTO districts (name, code, department_id, province_id, created_at, updated_at) VALUES
-- Provincia de Arequipa (ID: 1 de Arequipa)
('Arequipa', '01', 4, 1, NOW(), NOW()),
('Alto Selva Alegre', '02', 4, 1, NOW(), NOW()),
('Cayma', '03', 4, 1, NOW(), NOW()),
('Cerro Colorado', '04', 4, 1, NOW(), NOW()),
('Characato', '05', 4, 1, NOW(), NOW()),
('Chiguata', '06', 4, 1, NOW(), NOW()),
('Jacobo Hunter', '07', 4, 1, NOW(), NOW()),
('La Joya', '08', 4, 1, NOW(), NOW()),
('Mariano Melgar', '09', 4, 1, NOW(), NOW()),
('Miraflores', '10', 4, 1, NOW(), NOW()),
('Mollebaya', '11', 4, 1, NOW(), NOW()),
('Paucarpata', '12', 4, 1, NOW(), NOW()),
('Pocsi', '13', 4, 1, NOW(), NOW()),
('Polobaya', '14', 4, 1, NOW(), NOW()),
('Quequeña', '15', 4, 1, NOW(), NOW()),
('Sabandia', '16', 4, 1, NOW(), NOW()),
('Sachaca', '17', 4, 1, NOW(), NOW()),
('San Juan de Siguas', '18', 4, 1, NOW(), NOW()),
('San Juan de Tarucani', '19', 4, 1, NOW(), NOW()),
('Santa Isabel de Siguas', '20', 4, 1, NOW(), NOW()),
('Santa Rita de Siguas', '21', 4, 1, NOW(), NOW()),
('Socabaya', '22', 4, 1, NOW(), NOW()),
('Tiabaya', '23', 4, 1, NOW(), NOW()),
('Uchumayo', '24', 4, 1, NOW(), NOW()),
('Vitor', '25', 4, 1, NOW(), NOW()),
('Yanahuara', '26', 4, 1, NOW(), NOW()),
('Yarabamba', '27', 4, 1, NOW(), NOW()),
('Yura', '28', 4, 1, NOW(), NOW()),
('José Luis Bustamante y Rivero', '29', 4, 1, NOW(), NOW());
```

### **TRUJILLO (11 distritos)**
```sql
INSERT INTO districts (name, code, department_id, province_id, created_at, updated_at) VALUES
-- Provincia de Trujillo
('Trujillo', '01', 13, 1, NOW(), NOW()),
('El Porvenir', '02', 13, 1, NOW(), NOW()),
('Florencia de Mora', '03', 13, 1, NOW(), NOW()),
('Huanchaco', '04', 13, 1, NOW(), NOW()),
('La Esperanza', '05', 13, 1, NOW(), NOW()),
('Laredo', '06', 13, 1, NOW(), NOW()),
('Moche', '07', 13, 1, NOW(), NOW()),
('Poroto', '08', 13, 1, NOW(), NOW()),
('Salaverry', '09', 13, 1, NOW(), NOW()),
('Simbal', '10', 13, 1, NOW(), NOW()),
('Víctor Larco Herrera', '11', 13, 1, NOW(), NOW());
```

### **CUSCO (8 distritos principales)**
```sql
INSERT INTO districts (name, code, department_id, province_id, created_at, updated_at) VALUES
-- Provincia de Cusco
('Cusco', '01', 8, 1, NOW(), NOW()),
('Ccorca', '02', 8, 1, NOW(), NOW()),
('Poroy', '03', 8, 1, NOW(), NOW()),
('San Jerónimo', '04', 8, 1, NOW(), NOW()),
('San Sebastián', '05', 8, 1, NOW(), NOW()),
('Santiago', '06', 8, 1, NOW(), NOW()),
('Saylla', '07', 8, 1, NOW(), NOW()),
('Wanchaq', '08', 8, 1, NOW(), NOW());
```

### **PIURA (10 distritos)**
```sql
INSERT INTO districts (name, code, department_id, province_id, created_at, updated_at) VALUES
-- Provincia de Piura
('Piura', '01', 20, 1, NOW(), NOW()),
('Castilla', '02', 20, 1, NOW(), NOW()),
('Catacaos', '03', 20, 1, NOW(), NOW()),
('Cura Mori', '04', 20, 1, NOW(), NOW()),
('El Tallán', '05', 20, 1, NOW(), NOW()),
('La Arena', '06', 20, 1, NOW(), NOW()),
('La Unión', '07', 20, 1, NOW(), NOW()),
('Las Lomas', '08', 20, 1, NOW(), NOW()),
('Tambo Grande', '09', 20, 1, NOW(), NOW()),
('Veintiseis de Octubre', '10', 20, 1, NOW(), NOW());
```

---

## **4. SEEDERS ACTUALIZADOS PARA LARAVEL**

### **DepartmentSeeder.php**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
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
```
