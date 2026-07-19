<?php

namespace App\Observers;

use App\Models\Customer;

class CustomerObserver
{
    /**
     * Handle the Customer "creating" event.
     */
    public function creating(\App\Models\Customer $customer): void
    {
        // Link to existing user with same phone if exists
        if ($customer->phone && !$customer->user_id) {
            $user = \App\Models\User::where('phone', $customer->phone)->first();
            if ($user) {
                $customer->user_id = $user->id;
            }
        }
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(\App\Models\Customer $customer): void
    {
        // If phone changed and linked to user, should we sync back?
        // Usually User phone is the source of truth for authentication.
        if ($customer->isDirty('phone') && $customer->user_id) {
            $user = \App\Models\User::find($customer->user_id);
            if ($user && $user->phone !== $customer->phone) {
                $user->update(['phone' => $customer->phone]);
            }
        }
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        //
    }
}
