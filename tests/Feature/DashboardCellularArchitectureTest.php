<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Illuminate\Support\Facades\Gate;

class DashboardCellularArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = ['super_admin', 'admin', 'technician', 'receptionist', 'warehouse', 'accountant', 'customer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_repair_cell_gate_access()
    {
        $allowed = ['super_admin', 'admin', 'technician', 'receptionist'];
        $denied = ['warehouse', 'accountant', 'customer'];

        foreach ($allowed as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->assertTrue(Gate::forUser($user)->allows('view-repair-cell'), "Role $role should have access to repair cell");
        }

        foreach ($denied as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->assertFalse(Gate::forUser($user)->allows('view-repair-cell'), "Role $role should NOT have access to repair cell");
        }
    }

    public function test_sales_cell_gate_access()
    {
        $allowed = ['super_admin', 'admin', 'receptionist'];
        $denied = ['technician', 'warehouse', 'accountant', 'customer'];

        foreach ($allowed as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->assertTrue(Gate::forUser($user)->allows('view-sales-cell'), "Role $role should have access to sales cell");
        }

        foreach ($denied as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->assertFalse(Gate::forUser($user)->allows('view-sales-cell'), "Role $role should NOT have access to sales cell");
        }
    }

    public function test_warehouse_cell_gate_access()
    {
        $allowed = ['super_admin', 'admin', 'warehouse'];
        $denied = ['technician', 'receptionist', 'accountant', 'customer'];

        foreach ($allowed as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->assertTrue(Gate::forUser($user)->allows('view-warehouse-cell'), "Role $role should have access to warehouse cell");
        }

        foreach ($denied as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->assertFalse(Gate::forUser($user)->allows('view-warehouse-cell'), "Role $role should NOT have access to warehouse cell");
        }
    }

    public function test_accounting_cell_gate_access()
    {
        $allowed = ['super_admin', 'admin', 'accountant'];
        $denied = ['technician', 'receptionist', 'warehouse', 'customer'];

        foreach ($allowed as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->assertTrue(Gate::forUser($user)->allows('view-accounting-cell'), "Role $role should have access to accounting cell");
        }

        foreach ($denied as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->assertFalse(Gate::forUser($user)->allows('view-accounting-cell'), "Role $role should NOT have access to accounting cell");
        }
    }

    public function test_dashboard_renders_with_cells_for_admin()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/automation/dashboard');

        $response->assertStatus(200);
        $response->assertSee('cell-membrane', false);
        $response->assertSee('data-cell="repair"', false);
        $response->assertSee('data-cell="sales"', false);
        $response->assertSee('data-cell="warehouse"', false);
        $response->assertSee('data-cell="accounting"', false);
    }

    public function test_dashboard_renders_only_repair_for_technician()
    {
        /** @var \App\Models\User $tech */
        $tech = User::factory()->create();
        $tech->assignRole('technician');

        $response = $this->actingAs($tech)->get('/automation/dashboard');

        $response->assertStatus(200);
        $response->assertSee('data-cell="repair"', false);
        $response->assertDontSee('data-cell="sales"');
        $response->assertDontSee('data-cell="warehouse"');
        $response->assertDontSee('data-cell="accounting"');
    }
}
