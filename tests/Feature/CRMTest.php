<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CRMTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole($adminRole);

        $this->customer = Customer::factory()->create();
    }

    #[Test]
    public function admin_can_record_interaction()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('automation.customers.interactions.store', $this->customer), [
                'type' => 'call',
                'content' => 'Test interaction content',
                'interaction_date' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('customer_interactions', [
            'customer_id' => $this->customer->id,
            'type' => 'call',
            'content' => 'Test interaction content',
            'user_id' => $this->admin->id,
        ]);
    }

    #[Test]
    public function admin_can_see_interactions_on_customer_page()
    {
        $this->customer->interactions()->create([
            'user_id' => $this->admin->id,
            'type' => 'meeting',
            'content' => 'Meeting notes here',
            'interaction_date' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('automation.customers.show', $this->customer));

        $response->assertStatus(200);
        $response->assertSee('Meeting notes here');
        $response->assertSee('جلسه حضوری');
    }

    #[Test]
    public function interaction_requires_valid_data()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('automation.customers.interactions.store', $this->customer), [
                'type' => 'invalid_type',
                'content' => '',
                'interaction_date' => 'not-a-date',
            ]);

        $response->assertSessionHasErrors(['type', 'content', 'interaction_date']);
    }
}
