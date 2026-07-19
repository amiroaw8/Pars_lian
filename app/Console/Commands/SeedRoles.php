<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SeedRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default roles if they do not exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear permission cache first
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->info('کش دسترسی‌ها پاک شد.');

        $roles = [
            'super_admin' => 'سوپر ادمین',
            'admin' => 'مدیر سیستم',
            'technician' => 'تعمیرکار',
            'receptionist' => 'پذیرش',
            'warehouse' => 'انبار دار',
            'accountant' => 'حسابدار',
            'customer' => 'مشتری',
        ];

        foreach ($roles as $roleName => $label) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            
            if (!$role) {
                $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
                $this->info("نقش '{$label}' ({$roleName}) ایجاد شد. (ID: {$role->id})");
            } else {
                $this->info("نقش '{$label}' ({$roleName}) از قبل وجود دارد. (ID: {$role->id})");
            }
        }

        $this->info('تمام نقش‌های پیش‌فرض بررسی شدند.');
        
        // Final verification
        $count = Role::count();
        $this->info("تعداد کل نقش‌ها در دیتابیس: {$count}");
    }
}
