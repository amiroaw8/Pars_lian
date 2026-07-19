<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MultiRoleSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create necessary roles
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'technician']);
        Role::firstOrCreate(['name' => 'receptionist']);
        Role::firstOrCreate(['name' => 'warehouse']);
        Role::firstOrCreate(['name' => 'accountant']);
    }

    #[Test]
    public function a_user_can_have_multiple_roles()
    {
        $user = User::factory()->create();
        $user->syncRoles(['technician', 'receptionist']);

        $this->assertTrue($user->hasRole('technician'));
        $this->assertTrue($user->hasRole('receptionist'));
        $this->assertCount(2, $user->roles);
    }

    #[Test]
    public function user_role_display_name_concatenates_multiple_roles()
    {
        $user = User::factory()->create();
        $user->syncRoles(['technician', 'receptionist']);

        $displayName = $user->getRoleDisplayName();
        
        $this->assertStringContainsString('تعمیرکار', $displayName);
        $this->assertStringContainsString('پذیرش', $displayName);
        $this->assertStringContainsString('، ', $displayName);
    }

    #[Test]
    public function super_admin_can_access_user_management()
    {
        /** @var User $superAdmin */
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('super-admin.users.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_cannot_access_user_management()
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // We expect a 403 Forbidden
        // Since the routes are protected by role:super_admin middleware
        
        $response = $this->actingAs($admin)->get(route('super-admin.users.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function dashboard_shows_relevant_sections_for_combined_roles()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->syncRoles(['technician', 'warehouse']);

        $response = $this->actingAs($user)->get(route('automation.dashboard'));
        
        $response->assertStatus(200);
        // Check for technician related content (e.g., repairs)
        $response->assertSee('بخش تعمیرات و سرویس');
        // Check for warehouse related content (e.g., inventory)
        $response->assertSee('مدیریت انبار');
    }
}
