<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-super-admin {phone} {password} {name=Super Admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new super admin user or promote an existing one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $password = $this->argument('password');
        $name = $this->argument('name');

        $user = \App\Models\User::where('phone', $phone)->first();

        if ($user) {
            $this->info("User found with phone: {$phone}. Promoting to super_admin...");
            $user->password = \Illuminate\Support\Facades\Hash::make($password);
            $user->save();
        } else {
            $this->info("Creating new user with phone: {$phone}...");
            $user = \App\Models\User::create([
                'name' => $name,
                'phone' => $phone,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
            ]);
        }

        // Ensure super_admin role exists
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
        
        // Assign role
        $user->syncRoles([$role]);

        $this->info("Success! User {$phone} is now a Super Admin.");
        $this->info("You can now login at /login");
    }
}
