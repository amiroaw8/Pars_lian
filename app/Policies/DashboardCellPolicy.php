<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardCellPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the repair cell.
     */
    public function viewRepairCell(User $user): bool
    {
        return $user->isTechnician() || 
               $user->isReceptionist() || 
               $user->isAdmin() || 
               $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view the sales cell.
     */
    public function viewSalesCell(User $user): bool
    {
        return $user->isReceptionist() || 
               $user->isAdmin() || 
               $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view the warehouse cell.
     */
    public function viewWarehouseCell(User $user): bool
    {
        return $user->isWarehouseManager() || 
               $user->isAdmin() || 
               $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view the accounting cell.
     */
    public function viewAccountingCell(User $user): bool
    {
        return $user->isAccountant() || 
               $user->isAdmin() || 
               $user->isSuperAdmin();
    }
}
