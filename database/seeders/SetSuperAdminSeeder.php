<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SetSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set super admin role for the first user (assuming ID 3 is the main user)
        $user = \App\Models\User::find(3);
        if ($user) {
            $user->assignRole('super_admin');
            $this->command->info('کاربر ID 3 به نقش سوپر ادمین ارتقا یافت.');
        } else {
            $this->command->error('کاربر ID 3 یافت نشد.');
        }
    }
}
