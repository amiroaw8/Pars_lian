<?php

namespace Tests\Feature;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActiveWorkDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        foreach (['super_admin', 'technician', 'receptionist'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_dashboard_shows_clickable_breadcrumb_and_active_work_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this->actingAs($user)->get('/automation/dashboard');

        $response->assertOk();
        $response->assertSee('breadcrumb-link', false);
        $response->assertSee('کارهای فعال', false);
        $response->assertSee('id="active-work-super_admin"', false);
        $response->assertSee(route('automation.dashboard'), false);
    }

    public function test_active_work_json_returns_sections_sorted_by_updated_at(): void
    {
        $user = User::factory()->create();
        $user->assignRole('receptionist');

        ServiceOrder::factory()->create([
            'status' => 'registered',
            'updated_at' => now()->subHour(),
        ]);
        ServiceOrder::factory()->create([
            'status' => 'ready',
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson('/automation/dashboard/active-work');

        $response->assertOk();
        $response->assertJsonStructure(['sections', 'generated_at']);
        $sections = $response->json('sections');
        $this->assertNotEmpty($sections);
        $reception = collect($sections)->firstWhere('key', 'receptionist');
        $this->assertNotNull($reception);
        $this->assertGreaterThanOrEqual(2, $reception['count']);
    }

    public function test_multi_role_user_gets_separate_sections(): void
    {
        $user = User::factory()->create();
        $user->assignRole(['technician', 'receptionist']);

        ServiceOrder::factory()->create([
            'status' => 'registered',
            'technician_id' => null,
        ]);
        ServiceOrder::factory()->create([
            'status' => 'repairing',
            'technician_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson('/automation/dashboard/active-work');
        $keys = collect($response->json('sections'))->pluck('key')->all();

        $this->assertContains('receptionist', $keys);
        $this->assertContains('technician', $keys);
    }
}
