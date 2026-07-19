<?php

namespace Tests\Feature;

use App\Models\AccountingService;
use App\Models\Customer;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccountingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $this->user = User::factory()->create();
        $this->user->assignRole('accountant');
    }

    #[Test]
    public function user_can_view_accounting_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get(route('automation.accounting.index'));

        $response->assertOk();
        $response->assertSee('سیستم حسابداری');
    }

    #[Test]
    public function create_sale_route_redirects_to_pos(): void
    {
        $response = $this->actingAs($this->user)->get(route('automation.accounting.create-sale'));

        $response->assertRedirect(route('automation.pos.index'));
    }

    #[Test]
    public function user_can_create_service_transaction(): void
    {
        $serviceOrder = ServiceOrder::factory()->create();

        $response = $this->actingAs($this->user)->post(route('automation.accounting.store-service'), [
            'amount' => 50000,
            'description' => 'تعمیر کیبورد',
            'service_order_id' => $serviceOrder->id,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('automation.accounting.index'));
        $this->assertDatabaseHas('accounting_services', [
            'amount' => 50000,
            'description' => 'تعمیر کیبورد',
            'service_order_id' => $serviceOrder->id,
        ]);
    }

    #[Test]
    public function dashboard_lists_recorded_services(): void
    {
        AccountingService::factory()->count(2)->create(['amount' => 50000]);

        $response = $this->actingAs($this->user)->get(route('automation.accounting.index'));

        $response->assertOk();
        $response->assertViewHas('totalServices', 100000);
    }

    #[Test]
    public function service_requires_valid_service_order(): void
    {
        $response = $this->actingAs($this->user)->post(route('automation.accounting.store-service'), [
            'amount' => 50000,
            'description' => 'Test',
            'service_order_id' => 999,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('service_order_id');
    }
}
