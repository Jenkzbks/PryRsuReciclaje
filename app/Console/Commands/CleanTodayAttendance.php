<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Carbon\Carbon;

class CleanTodayAttendance extends Command
{
    protected $signature = 'attendance:clean-today {employee_id}';
    protected $description = 'Clean today attendance for testing';

    public function handle()
    {
        $employeeId = $this->argument('employee_id');
        
        $deleted = Attendance::where('employee_id', $employeeId)
            ->whereDate('date', Carbon::today())
            ->delete();
            
        $this->info("Eliminadas {$deleted} asistencias de hoy para empleado {$employeeId}");
        
        return 0;
    }
}