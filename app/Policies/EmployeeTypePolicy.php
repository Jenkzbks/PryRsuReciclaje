<?php

namespace App\Policies;

use App\Models\EmployeeType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeTypePolicy
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
    public function view(User $user, EmployeeType $employeeType)
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
    public function update(User $user, EmployeeType $employeeType)
    {
        // No se pueden editar tipos protegidos
        if ($employeeType->protected) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmployeeType $employeeType)
    {
        // No se pueden eliminar tipos protegidos
        if ($employeeType->protected) {
            return false;
        }

        // No se pueden eliminar si tienen empleados asociados
        if ($employeeType->employees()->count() > 0) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EmployeeType $employeeType)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EmployeeType $employeeType)
    {
        return !$employeeType->protected;
    }
}