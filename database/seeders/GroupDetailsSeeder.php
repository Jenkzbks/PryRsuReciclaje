<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupDetailsSeeder extends Seeder
{
    public function run(): void
    {
        // Migration columns: scheduling_id, emplooyee_id
        $schedulingId = \App\Models\Scheduling::first()?->id ?? 1;
        $employeeId = \App\Models\Employee::first()?->id ?? 1;

        DB::table('groupdetails')->insert([
            ['scheduling_id' => $schedulingId, 'emplooyee_id' => $employeeId, 'created_at' => now(), 'updated_at' => now()],
            ['scheduling_id' => $schedulingId, 'emplooyee_id' => $employeeId, 'created_at' => now(), 'updated_at' => now()],
            ['scheduling_id' => $schedulingId, 'emplooyee_id' => $employeeId, 'created_at' => now(), 'updated_at' => now()],
            ['scheduling_id' => $schedulingId, 'emplooyee_id' => $employeeId, 'created_at' => now(), 'updated_at' => now()],
            ['scheduling_id' => $schedulingId, 'emplooyee_id' => $employeeId, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
