<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractPolicy
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
    public function view(User $user, Contract $contract)
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
    public function update(User $user, Contract $contract)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contract $contract)
    {
        // No se pueden eliminar contratos activos
        if ($contract->is_active) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contract $contract)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contract $contract)
    {
        return !$contract->is_active;
    }

    /**
     * Verificar si se puede activar un contrato
     */
    public function activate(User $user, Contract $contract)
    {
        // Verificar que no hay otro contrato activo
        $activeContracts = Contract::where('employee_id', $contract->employee_id)
            ->where('id', '!=', $contract->id)
            ->where('is_active', true)
            ->count();

        return $activeContracts === 0;
    }

    /**
     * Verificar si se puede crear un contrato eventual
     */
    public function createEventual(User $user, $employeeId, $startDate)
    {
        return Contract::canCreateEventualContract($employeeId, $startDate);
    }
}