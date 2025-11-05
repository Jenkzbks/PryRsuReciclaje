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
        ]);
    }
}
