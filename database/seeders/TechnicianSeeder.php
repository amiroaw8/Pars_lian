<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TechnicianSeeder extends Seeder
{
    public function run(): void
    {
        $technicians = [
            [
                'name' => 'تعمیرکار اول',
                'email' => 'tech1@example.com',
            ],
            [
                'name' => 'تعمیرکار دوم',
                'email' => 'tech2@example.com',
            ],
        ];

        foreach ($technicians as $index => $tech) {
            $user = User::firstOrCreate(
                ['email' => $tech['email']],
                [
                    'name' => $tech['name'],
                    'password' => Hash::make('password'),
                    'phone' => '0912000000' . ($index + 1),
                ]
            );

            $user->assignRole('technician');
        }
    }
}
