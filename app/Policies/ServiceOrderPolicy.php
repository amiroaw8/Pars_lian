<?php

namespace App\Policies;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServiceOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || 
               $user->isSuperAdmin() || 
               $user->isReceptionist() || 
               $user->isTechnician() || 
               $user->isAccountant();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceOrder $serviceOrder): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin() || $user->isReceptionist() || $user->isAccountant()) {
            return true;
        }

        if ($user->isTechnician()) {
            return $serviceOrder->technician_id === $user->id;
        }

        return $user->hasRole('customer')
            && $user->customer
            && (int) $serviceOrder->customer_id === (int) $user->customer->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->isReceptionist();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceOrder $serviceOrder): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin() || $user->isReceptionist()) {
            return true;
        }

        if ($user->isTechnician()) {
            // Technician can update only if assigned
            return $serviceOrder->technician_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can perform attachment uploads.
     */
    public function uploadAttachment(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->isSuperAdmin()
            || $user->isAdmin()
            || $user->isReceptionist()
            || $user->isTechnician()
            || $user->isAccountant();
    }

    /**
     * Determine whether the user can perform repair-related actions.
     */
    public function manageRepair(User $user, ServiceOrder $serviceOrder): bool
    {
        return $this->performTechnicalAction($user, $serviceOrder)
            || $this->performFinancialAction($user, $serviceOrder);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can perform technical actions (start repair, add parts, etc.)
     */
    public function performTechnicalAction(User $user, ServiceOrder $serviceOrder): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }
        
        return $user->isTechnician() && $serviceOrder->technician_id === $user->id;
    }

    /**
     * Determine whether the user can perform financial actions (invoice, payment)
     */
    public function performFinancialAction(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->isAccountant() || $user->isReceptionist();
    }
}
