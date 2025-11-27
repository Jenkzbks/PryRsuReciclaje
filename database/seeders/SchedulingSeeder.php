<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeGroup;
use App\Models\Zone;
use App\Models\Shift;
use App\Models\Vehicle;
use Carbon\Carbon;

class SchedulingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group = EmployeeGroup::first();
        $zone = Zone::first();
        $shift = Shift::first();
        $vehicle = Vehicle::first();

        $groupId = $group ? $group->id : 1;
        $zoneId = $zone ? $zone->id : 1;
        $shiftId = $shift ? $shift->id : 1;
        $vehicleId = $vehicle ? $vehicle->id : 1;

        DB::table('schedulings')->insert([
            [
                'group_id' => $groupId,
                'shift_id' => $shiftId,
                'vehicle_id' => $vehicleId,
                'zone_id' => $zoneId,
                'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'notes' => 'Recolección regular',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_id' => $groupId,
                'shift_id' => $shiftId,
                'vehicle_id' => $vehicleId,
                'zone_id' => $zoneId,
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'notes' => 'Recolección especial',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
