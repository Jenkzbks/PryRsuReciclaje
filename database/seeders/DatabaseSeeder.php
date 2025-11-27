<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            ProvinceSeeder::class,
            DistrictSeeder::class,
            BrandSeeder::class,
            BrandModelSeeder::class,
            ColorSeeder::class,
            VehicleTypeSeeder::class,
            UserSeeder::class,
            ShiftSeeder::class,
            ZoneSeeder::class,
            VehicleSeeder::class,
            SimplePersonnelSeeder::class,
            EmployeeGroupSeeder::class,
            SchedulingSeeder::class,
            // Newly added seeders to cover remaining migrations
            VehiclesImageSeeder::class,
            ZoneCoordsSeeder::class,
            ZoneVehicleSeeder::class,
            ZoneShiftSeeder::class,
            RoutesSeeder::class,
            RouteCoordsSeeder::class,
            MaintenancesSeeder::class,
            MaintenanceSchedulesSeeder::class,
            MaintenanceRecordsSeeder::class,
            ConfigGroupsSeeder::class,
            GroupDetailsSeeder::class,
        ]);
    }
}
