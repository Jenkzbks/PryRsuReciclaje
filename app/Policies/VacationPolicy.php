<?php

namespace App\Policies;

use App\Models\Vacation;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;

class VacationPolicy
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
    public function view(User $user, Vacation $vacation)
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
    public function update(User $user, Vacation $vacation)
    {
        // Solo se pueden editar vacaciones pendientes
        return $vacation->status === Vacation::STATUS_PENDING;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vacation $vacation)
    {
        // Solo se pueden eliminar vacaciones pendientes o rechazadas
        return in_array($vacation->status, [
            Vacation::STATUS_PENDING,
            Vacation::STATUS_REJECTED
        ]);
    }

    /**
     * Verificar si un empleado puede solicitar vacaciones
     */
    public function request(User $user, $employeeId)
    {
        $employee = Employee::find($employeeId);
        
        if (!$employee) {
            return false;
        }

        return $employee->canRequestVacations();
    }

    /**
     * Verificar si se puede aprobar una vacación
     */
    public function approve(User $user, Vacation $vacation)
    {
        return $vacation->status === Vacation::STATUS_PENDING;
    }

    /**
     * Verificar si se puede rechazar una vacación
     */
    public function reject(User $user, Vacation $vacation)
    {
        return $vacation->status === Vacation::STATUS_PENDING;
    }

    /**
     * Verificar si se puede cancelar una vacación
     */
    public function cancel(User $user, Vacation $vacation)
    {
        return in_array($vacation->status, [
            Vacation::STATUS_PENDING,
            Vacation::STATUS_APPROVED
        ]);
    }

    /**
     * Verificar si se puede completar una vacación
     */
    public function complete(User $user, Vacation $vacation)
    {
        return $vacation->status === Vacation::STATUS_APPROVED 
            && $vacation->end_date < now();
    }
}