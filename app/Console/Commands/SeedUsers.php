<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SeedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-users {--force : Force recreate users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test user for each available role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Ensure roles exist first
        $this->call('app:seed-roles');
        $this->newLine();

        $rolesData = [
            'super_admin' => ['name' => 'Super Admin', 'phone' => '09120000001', 'email' => 'superadmin@pars-lian.com'],
            'admin' => ['name' => 'System Admin', 'phone' => '09120000002', 'email' => 'admin@pars-lian.com'],
            'technician' => ['name' => 'Technician User', 'phone' => '09120000003', 'email' => 'tech@pars-lian.com'],
            'receptionist' => ['name' => 'Receptionist User', 'phone' => '09120000004', 'email' => 'reception@pars-lian.com'],
            'warehouse' => ['name' => 'Warehouse Manager', 'phone' => '09120000005', 'email' => 'warehouse@pars-lian.com'],
            'accountant' => ['name' => 'Accountant User', 'phone' => '09120000006', 'email' => 'accountant@pars-lian.com'],
            'customer' => ['name' => 'Customer User', 'phone' => '09120000007', 'email' => 'customer@pars-lian.com'],
        ];

        $defaultPassword = 'password';

        foreach ($rolesData as $roleName => $userData) {
            $this->info("Processing user for role: {$roleName}...");

            // Check if role exists
            if (!Role::where('name', $roleName)->exists()) {
                $this->error("  Role '{$roleName}' does not exist! Skipping.");
                continue;
            }

            // Check if user exists by phone (unique identifier)
            $user = User::where('phone', $userData['phone'])->first();

            if ($user && $this->option('force')) {
                $this->warn("  User exists. Deleting to recreate (--force)...");
                $user->forceDelete();
                $user = null;
            }

            if (!$user) {
                $user = User::create([
                    'name' => $userData['name'],
                    'phone' => $userData['phone'],
                    'email' => $userData['email'],
                    'password' => Hash::make($defaultPassword),
                ]);
                $this->info("  Created user: {$userData['name']} ({$userData['phone']})");
            } else {
                $this->line("  User already exists: {$user->name}");
            }

            // Sync role
            if (!$user->hasRole($roleName)) {
                $user->syncRoles([$roleName]);
                $this->info("  Assigned role '{$roleName}' to user.");
            } else {
                $this->line("  User already has role '{$roleName}'.");
            }
            
            $this->newLine();
        }

        $this->info('All role-based users processed successfully.');
        $this->info("Default Password: {$defaultPassword}");
    }
}
