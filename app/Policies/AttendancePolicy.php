<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;
use Carbon\Carbon;

class AttendancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendance $attendance)
    {
        // Solo se pueden editar asistencias del día actual
        return $attendance->datetime->isToday();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance)
    {
        // Solo se pueden eliminar asistencias del día actual
        return $attendance->datetime->isToday();
    }

    /**
     * Verificar si un empleado puede registrar entrada
     */
    public function clockIn(User $user, $employeeId)
    {
        $employee = Employee::find($employeeId);
        
        if (!$employee) {
            return false;
        }

        // Verificar si ya tiene una entrada registrada hoy
        $todayEntry = Attendance::where('employee_id', $employeeId)
            ->whereDate('datetime', Carbon::today())
            ->where('type', Attendance::TYPE_ENTRY)
            ->first();

        return !$todayEntry;
    }

    /**
     * Verificar si un empleado puede registrar salida
     */
    public function clockOut(User $user, $employeeId)
    {
        $employee = Employee::find($employeeId);
        
        if (!$employee) {
            return false;
        }

        // Verificar si tiene una entrada registrada hoy sin salida
        $todayEntry = Attendance::where('employee_id', $employeeId)
            ->whereDate('datetime', Carbon::today())
            ->where('type', Attendance::TYPE_ENTRY)
            ->first();

        $todayExit = Attendance::where('employee_id', $employeeId)
            ->whereDate('datetime', Carbon::today())
            ->where('type', Attendance::TYPE_EXIT)
            ->first();

        return $todayEntry && !$todayExit;
    }

    /**
     * Verificar si se puede modificar una asistencia específica
     */
    public function modify(User $user, Attendance $attendance)
    {
        // Solo se pueden modificar asistencias de los últimos 3 días
        return $attendance->datetime >= Carbon::now()->subDays(3);
    }

    /**
     * Verificar si se puede generar reporte de asistencias
     */
    public function generateReport(User $user)
    {
        return true;
    }

    /**
     * Verificar si se puede corregir asistencia manualmente
     */
    public function correctAttendance(User $user, $employeeId)
    {
        return true; // Los administradores pueden corregir asistencias
    }
}