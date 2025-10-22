<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Carbon\Carbon;

class CheckAttendance extends Command
{
    protected $signature = 'attendance:check {employee_id}';
    protected $description = 'Check attendance for employee';

    public function handle()
    {
        $employeeId = $this->argument('employee_id');
        
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereDate('date', Carbon::today())
            ->get();
            
        if ($attendances->isEmpty()) {
            $this->info("No hay asistencias para el empleado {$employeeId} hoy.");
            return 0;
        }
        
        foreach ($attendances as $att) {
            $this->info("=== ASISTENCIA ID: {$att->id} ===");
            $this->info("Fecha: {$att->date}");
            $this->info("Check In: " . ($att->check_in ? $att->check_in->format('H:i:s') : 'NULL'));
            $this->info("Check Out: " . ($att->check_out ? $att->check_out->format('H:i:s') : 'NULL'));
            $this->info("Status: {$att->status}");
            $this->info("Horas: " . ($att->hours_worked ?? 'NULL'));
            $this->info("Creado: {$att->created_at}");
            $this->info("Actualizado: {$att->updated_at}");
            $this->info("---");
        }
        
        return 0;
    }
}