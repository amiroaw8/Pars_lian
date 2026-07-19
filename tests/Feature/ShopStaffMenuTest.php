<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShopStaffMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'warehouse', 'guard_name' => 'web']);
    }

    public function test_staff_is_redirected_from_customer_dashboard(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('warehouse');

        $this->actingAs($user)
            ->withSession(['two_factor_verified' => true])
            ->get(route('customer.dashboard'))
            ->assertRedirect(route('auth.dashboard'))
            ->assertSessionHas('info');
    }

    public function test_homepage_shows_staff_panel_link_for_warehouse_user(): void
    {
        $user = User::factory()->create(['name' => 'کاربر انبار', 'is_active' => true]);
        $user->assignRole('warehouse');

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('پنل مدیریت')
            ->assertSee('میز کار')
            ->assertDontSee('پنل کاربری');
    }
}
