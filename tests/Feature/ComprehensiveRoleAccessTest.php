<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\ServiceOrder;
use App\Models\Customer;
use App\Models\Device;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ComprehensiveRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = ['super_admin', 'admin', 'technician', 'receptionist', 'warehouse', 'accountant', 'customer', 'employee'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    /**
     * Test admin panel access
     */
    public function test_only_super_admins_can_access_system_admin()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['super_admin']);
        $this->actingAs($user)->withSession(['two_factor_verified' => true])->get('/dashboard')->assertRedirect(route('super-admin.dashboard'));
        $this->actingAs($user)->withSession(['two_factor_verified' => true])->get(route('super-admin.dashboard'))->assertStatus(200);

        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);
        $this->actingAs($admin)->withSession(['two_factor_verified' => true])->get('/dashboard')->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test regular admin panel access
     */
    public function test_admins_can_access_panel()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['admin']);
        $response = $this->actingAs($user)->withSession(['two_factor_verified' => true])->get('/panel');
        if ($response->status() !== 200) {
            dump($response->status(), $response->getContent());
        }
        $response->assertStatus(200);

        /** @var User $tech */
        $tech = User::factory()->create();
        $tech->syncRoles(['technician']);
        $this->actingAs($tech)->withSession(['two_factor_verified' => true])->get('/panel')->assertStatus(403);
    }

    /**
     * Test automation dashboard access
     */
    public function test_staff_roles_can_access_automation_dashboard()
    {
        $staffRoles = ['technician', 'receptionist', 'warehouse', 'accountant', 'employee'];

        foreach ($staffRoles as $roleName) {
            /** @var User $user */
            $user = User::factory()->create();
            $user->syncRoles([$roleName]);
            $this->actingAs($user)->withSession(['two_factor_verified' => true])->get('/dashboard')->assertRedirect(route('automation.dashboard'));
        }

        /** @var User $customer */
        $customer = User::factory()->create();
        $customer->syncRoles(['customer']);
        $this->actingAs($customer)->get('/dashboard')->assertRedirect(route('customer.dashboard'));
    }

    /**
     * Test product management access
     */
    public function test_only_admins_can_manage_products()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['admin']);
        $this->actingAs($user)->withSession(['two_factor_verified' => true])->get('/panel/products')->assertStatus(200);

        /** @var User $tech */
        $tech = User::factory()->create();
        $tech->syncRoles(['technician']);
        $this->actingAs($tech)->withSession(['two_factor_verified' => true])->get('/panel/products')->assertStatus(403);
    }

    /**
     * Test customer access to their own data
     */
    public function test_customer_can_access_their_own_dashboard()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['customer']);

        $this->actingAs($user)->get('/my-account/dashboard')->assertStatus(200);
        $this->actingAs($user)->get('/my-account/orders')->assertStatus(200);
    }

    /**
     * Test receptionist access to shop orders
     */
    public function test_receptionist_can_access_shop_orders()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['receptionist']);
        
        $this->actingAs($user)->withSession(['two_factor_verified' => true])->get(route('automation.orders.index'))->assertStatus(200);
    }

    /**
     * Technician self-assignment is disabled; reception assigns during create/edit.
     */
    public function test_technician_cannot_assign_self_to_repair()
    {
        /** @var User $tech */
        $tech = User::factory()->create();
        $tech->syncRoles(['technician']);

        $serviceOrder = ServiceOrder::factory()->create([
            'technician_id' => null,
            'status' => \App\Enums\ServiceOrderStatus::REGISTERED,
        ]);

        $response = $this->actingAs($tech)
            ->post(route('automation.service-orders.assign-self', $serviceOrder));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNull($serviceOrder->fresh()->technician_id);
    }

    /**
     * Test activity logs access
     */
    public function test_only_admins_can_access_activity_logs()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['admin']);
        $this->actingAs($user)->withSession(['two_factor_verified' => true])->get(route('admin.activity-logs.index'))->assertStatus(200);

        /** @var User $receptionist */
        $receptionist = User::factory()->create();
        $receptionist->syncRoles(['receptionist']);
        $this->actingAs($receptionist)->withSession(['two_factor_verified' => true])->get(route('admin.activity-logs.index'))->assertStatus(403);
    }

    /**
     * Test recycle bin access
     */
    public function test_only_admins_can_access_recycle_bin()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['admin']);
        $this->actingAs($user)->withSession(['two_factor_verified' => true])->get(route('admin.recycle-bin.index'))->assertStatus(200);

        /** @var User $tech */
        $tech = User::factory()->create();
        $tech->syncRoles(['technician']);
        $this->actingAs($tech)->withSession(['two_factor_verified' => true])->get(route('admin.recycle-bin.index'))->assertStatus(403);
    }

    /**
     * Test dashboard notifications API access for authorized users
     */
    public function test_authorized_users_can_access_notifications_api()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['technician']);
        $this->actingAs($user)->withSession(['two_factor_verified' => true])->get(route('api.notifications.summary'))->assertStatus(200);
    }

    public function test_guests_are_unauthorized_on_notifications_api()
    {
        $this->get(route('api.notifications.summary'))->assertStatus(401);
    }

    /**
     * Test guests are redirected from protected routes
     */
    public function test_guests_are_redirected_from_protected_routes()
    {
        $this->get('/automation/dashboard')->assertRedirect('/login');
        $this->get('/system-admin')->assertRedirect('/login');
        $this->get('/my-account/dashboard')->assertRedirect('/login');
    }
}
