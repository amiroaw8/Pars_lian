<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'super_admin' => 'سوپر ادمین',
            'admin' => 'مدیر سیستم',
            'technician' => 'تعمیرکار',
            'receptionist' => 'پذیرش',
            'warehouse' => 'انبار دار',
            'accountant' => 'حسابدار',
            'customer' => 'مشتری',
        ];

        foreach ($roles as $roleName => $displayName) {
            // Create role if it doesn't exist
            $role = Role::firstOrCreate(['name' => $roleName]);

            $email = "{$roleName}@pars-lian.com";
            
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $displayName,
                    'password' => Hash::make('password'),
                    'phone' => $this->getRandomPhone(),
                ]
            );

            // Assign role to user
            $user->assignRole($role);

            $this->command->info("User for role '{$roleName}' created/updated and role assigned: {$email} (Password: password)");
        }
    }

    /**
     * Generate a random phone number for users
     */
    private function getRandomPhone(): string
    {
        return '0912' . rand(1000000, 9999999);
    }
}
