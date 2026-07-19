<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SampleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'مدیر سیستم',
                'phone' => '09123456789',
                'email' => 'admin@parslian.ir',
                'role' => 'admin',
                'password' => 'admin123',
            ],
            [
                'name' => 'احمد تکنسین',
                'phone' => '09123456788',
                'email' => 'technician@parslian.ir',
                'role' => 'technician',
                'password' => 'tech123',
            ],
            [
                'name' => 'مریم پذیرش',
                'phone' => '09123456787',
                'email' => 'reception@parslian.ir',
                'role' => 'receptionist',
                'password' => 'recep123',
            ],
            [
                'name' => 'علی انباردار',
                'phone' => '09123456786',
                'email' => 'warehouse@parslian.ir',
                'role' => 'warehouse',
                'password' => 'ware123',
            ],
            [
                'name' => 'فاطمه حسابدار',
                'phone' => '09123456785',
                'email' => 'accountant@parslian.ir',
                'role' => 'accountant',
                'password' => 'acc123',
            ],
        ];

        foreach ($users as $userData) {
            // Check if user already exists
            $existingUser = \App\Models\User::where('phone', $userData['phone'])
                ->orWhere('email', $userData['email'])
                ->first();

            if (! $existingUser) {
                $user = \App\Models\User::create([
                    'name' => $userData['name'],
                    'phone' => $userData['phone'],
                    'email' => $userData['email'],
                    'password' => bcrypt($userData['password']),
                ]);

                $user->assignRole($userData['role']);

                $this->command->info("کاربر {$userData['name']} ایجاد شد.");
            } else {
                $this->command->info("کاربر {$userData['name']} از قبل وجود دارد.");
            }
        }

        $this->command->info('تمام کاربران نمونه ایجاد شدند.');
        $this->command->info('');
        $this->command->info('اطلاعات ورود:');
        $this->command->info('مدیر سیستم: admin@parslian.ir / admin123');
        $this->command->info('تکنسین: technician@parslian.ir / tech123');
        $this->command->info('پذیرش: reception@parslian.ir / recep123');
        $this->command->info('انبار دار: warehouse@parslian.ir / ware123');
        $this->command->info('حسابدار: accountant@parslian.ir / acc123');
    }
}
