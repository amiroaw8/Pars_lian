<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\ServiceOrder;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = ['super_admin', 'admin', 'technician', 'receptionist', 'warehouse', 'accountant', 'customer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function getCommonStaffRoles()
    {
        return ['technician', 'receptionist', 'warehouse', 'accountant'];
    }

    /**
     * @dataProvider roleRouteProvider
     */
    public function test_role_access_to_routes($role, $route, $expectedStatus)
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)
            ->withSession(['two_factor_verified' => true])
            ->get($route);

        if ($response->status() !== $expectedStatus) {
            dump("Failed: Role $role on $route expected $expectedStatus, got " . $response->status());
        }

        $response->assertStatus($expectedStatus);
    }

    public static function roleRouteProvider()
    {
        return [
            // Super Admin
            ['super_admin', '/system-admin', 200],
            ['super_admin', '/panel/activity-logs', 200],
            ['super_admin', '/panel/recycle-bin', 200],
            ['super_admin', '/panel/settings', 200],

            // Admin
            ['admin', '/panel', 200],
            ['admin', '/panel/products', 200],
            ['admin', '/panel/categories', 200],
            ['admin', '/panel/activity-logs', 200],
            ['admin', '/system-admin', 403],

            // Technician (dashboard/cartable redirects to specific ones)
            ['technician', '/automation/dashboard', 200], 
            ['technician', '/panel', 403],

            // Receptionist
            ['receptionist', '/automation/dashboard', 200],
            ['receptionist', '/automation/orders', 200],
            ['receptionist', '/panel', 403],

            // Warehouse
            ['warehouse', '/automation/inventory', 200],
            ['warehouse', '/panel/products', 403], // Restricted to admin role by middleware

            // Accountant
            ['accountant', '/automation/accounting', 200],
            ['accountant', '/panel', 403],
            
            // Customer
            ['customer', '/my-account/dashboard', 200],
            ['customer', '/panel', 403],
            ['customer', '/automation/dashboard', 403], // Blocked by role middleware
        ];
    }

    /**
     * Test 2-hour staff session timeout logic
     */
    public function test_staff_session_timeout_is_applied()
    {
        /** @var User $staff */
        $staff = User::factory()->create();
        $staff->assignRole('technician');

        // Set last activity to 3 hours ago in the session
        $this->actingAs($staff)
            ->withSession([
                'last_activity_time' => now()->subHours(3)->timestamp,
                'two_factor_verified' => true
            ]);

        // This request should trigger the StaffSessionTimeout middleware
        $response = $this->get('/automation/dashboard');

        // It should redirect to login due to timeout
        $response->assertRedirect('/login');
        $this->assertTrue(\Illuminate\Support\Facades\Auth::guest());
    }
}
