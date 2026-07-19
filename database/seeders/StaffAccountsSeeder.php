<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffRoles = [
            'super_admin' => [
                'name' => 'سوپر ادمین سیستم',
                'email' => 'superadmin@parslian.ir',
                'phone' => '09001111111'
            ],
            'admin' => [
                'name' => 'مدیر اجرایی',
                'email' => 'admin@parslian.ir',
                'phone' => '09002222222'
            ],
            'technician' => [
                'name' => 'تکنسین فنی',
                'email' => 'tech@parslian.ir',
                'phone' => '09003333333'
            ],
            'receptionist' => [
                'name' => 'مسئول پذیرش',
                'email' => 'reception@parslian.ir',
                'phone' => '09004444444'
            ],
            'warehouse' => [
                'name' => 'مدیر انبار',
                'email' => 'warehouse@parslian.ir',
                'phone' => '09005555555'
            ],
            'accountant' => [
                'name' => 'حسابدار سیستم',
                'email' => 'accountant@parslian.ir',
                'phone' => '09006666666'
            ],
        ];

        $password = 'password123';

        foreach ($staffRoles as $roleName => $details) {
            // Ensure role exists
            Role::firstOrCreate(['name' => $roleName]);

            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $details['email']],
                [
                    'name' => $details['name'],
                    'phone' => $details['phone'],
                    'password' => Hash::make($password),
                ]
            );

            // Sync role (ensure they only have this role for now, or use assignRole if you want to keep existing)
            $user->syncRoles([$roleName]);

            $this->command->info("اکانت {$details['name']} با نقش {$roleName} ساخته شد.");
        }

        $this->command->warn("\nاطلاعات ورود پرسنل:");
        $this->command->info("رمز عبور همگانی: {$password}");
        foreach ($staffRoles as $roleName => $details) {
            $this->command->line("- {$details['name']} ({$roleName}): {$details['email']}");
        }
    }
}
