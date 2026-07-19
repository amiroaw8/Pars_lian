<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SessionAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['session.driver' => 'database']);
        config(['session.max_active' => 2]);

        foreach (['receptionist', 'customer'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_guest_can_post_logout_without_error(): void
    {
        $this->post(route('logout'))
            ->assertRedirect(route('home'));
    }

    public function test_get_logout_performs_logout_and_redirects(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($user)
            ->get('/logout')
            ->assertRedirect(route('home'))
            ->assertSessionHas('success');

        $this->assertGuest();
    }

    public function test_logout_invalidates_session_and_redirects_to_home(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('success');

        $this->assertGuest();
    }

    public function test_staff_over_session_limit_redirected_after_login(): void
    {
        $user = User::factory()->create(['phone' => '09125555555', 'is_active' => true]);
        $user->assignRole('receptionist');
        $user->forceFill(['password' => bcrypt('password')])->save();

        foreach (['sess-a', 'sess-b', 'sess-c'] as $sessionId) {
            DB::table('sessions')->insert([
                'id' => $sessionId,
                'user_id' => $user->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test',
                'payload' => base64_encode(serialize([])),
                'last_activity' => time(),
            ]);
        }

        $this->post(route('login'), [
            'phone' => '09125555555',
            'password' => 'password',
        ])->assertRedirect(route('auth.sessions.limit'));
    }

    public function test_session_limit_page_accessible_during_two_factor(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('receptionist');

        foreach (['sess-1', 'sess-2', 'sess-3'] as $sessionId) {
            DB::table('sessions')->insert([
                'id' => $sessionId,
                'user_id' => $user->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test',
                'payload' => base64_encode(serialize([])),
                'last_activity' => time(),
            ]);
        }

        $response = $this->actingAs($user)->get(route('auth.sessions.limit'));

        $response->assertOk();
        $response->assertSee('محدودیت نشست');
    }
}
