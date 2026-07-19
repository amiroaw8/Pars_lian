<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // ایجاد نقش‌ها برای تست
        $roles = ['super_admin', 'admin', 'technician', 'receptionist', 'warehouse', 'accountant', 'customer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    public function test_home_page_is_accessible()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
    }

    public function test_super_admin_can_access_system_admin_dashboard()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['super_admin']);

        $response = $this->actingAs($user)->get(route('super-admin.dashboard'));
        
        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_automation_dashboard()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['customer']);

        $response = $this->actingAs($user)->get(route('automation.dashboard'));
        
        $response->assertStatus(403);
    }

    public function test_technician_can_access_repair_module()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['technician']);

        $response = $this->actingAs($user)->get(route('automation.repairs.index'));
        
        $response->assertStatus(200);
    }

    public function test_warehouse_manager_can_access_inventory()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['warehouse']);

        $response = $this->actingAs($user)->get(route('automation.inventory.index'));
        
        $response->assertStatus(200);
    }

    public function test_accountant_can_access_accounting_module()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['accountant']);

        $response = $this->actingAs($user)->get(route('automation.accounting.index'));
        
        $response->assertStatus(200);
    }
}
