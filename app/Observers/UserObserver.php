<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Link to existing customer with same phone if exists
        if ($user->phone) {
            $customer = \App\Models\Customer::where('phone', $user->phone)
                ->whereNull('user_id')
                ->first();
            
            if ($customer) {
                $customer->update(['user_id' => $user->id]);
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Sync phone changes to linked customer
        if ($user->isDirty('phone')) {
            $customer = \App\Models\Customer::where('user_id', $user->id)->first();
            if ($customer) {
                $customer->update(['phone' => $user->phone]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
