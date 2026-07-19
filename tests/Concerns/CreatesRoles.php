<?php

namespace Tests\Concerns;

use App\Models\User;
use Spatie\Permission\Models\Role;

trait CreatesRoles
{
    protected function seedRoles(array $roles = ['super_admin', 'admin', 'technician', 'receptionist', 'warehouse', 'accountant', 'customer']): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    protected function createUserWithRole(string $role, array $attributes = []): User
    {
        $this->seedRoles();

        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    protected function actingAsStaff(User $user)
    {
        return $this->actingAs($user)->withSession(['two_factor_verified' => true]);
    }
}
