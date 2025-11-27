<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigGroupsSeeder extends Seeder
{
    public function run(): void
    {
        // Migration columns: employeegroup_id, employee_id, posicion
        $groupId = \App\Models\EmployeeGroup::first()?->id ?? 1;
        $employeeId = \App\Models\Employee::first()?->id ?? 1;

        DB::table('configgroups')->insert([
            ['employeegroup_id' => $groupId, 'employee_id' => $employeeId, 'posicion' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['employeegroup_id' => $groupId, 'employee_id' => $employeeId, 'posicion' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['employeegroup_id' => $groupId, 'employee_id' => $employeeId, 'posicion' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['employeegroup_id' => $groupId, 'employee_id' => $employeeId, 'posicion' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['employeegroup_id' => $groupId, 'employee_id' => $employeeId, 'posicion' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
