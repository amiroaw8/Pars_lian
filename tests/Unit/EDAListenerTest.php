<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ServiceOrderStatus;
use App\Events\OrderStatusChanged;
use App\Events\PaymentStatusChanged;
use App\Events\ServiceOrderStatusChanged;
use App\Listeners\SyncOrderInventory;
use App\Listeners\SyncOrderToAccounting;
use App\Listeners\SyncServiceOrderToAccounting;
use App\Models\AccountingSale;
use App\Models\AccountingService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Models\Customer;
use App\Services\AccountingManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

use Spatie\Permission\Models\Role;

class EDAListenerTest extends TestCase
{
    use RefreshDatabase;

    private AccountingManager $accountingManager;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary roles for UserFactory
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'technician']);
        Role::create(['name' => 'receptionist']);
        Role::create(['name' => 'warehouse']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'customer']);

        $this->accountingManager = new AccountingManager();
    }

    /**
     * Test SyncOrderToAccounting listener.
     */
    public function test_sync_order_to_accounting_records_sale_when_paid()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $user->id]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_status' => PaymentStatus::PAID,
            'total' => 1000,
            'order_number' => 'ORD-123',
            'payment_method' => 'card'
        ]);

        $event = new PaymentStatusChanged($order);
        $listener = new SyncOrderToAccounting($this->accountingManager);

        $listener->handle($event);

        $this->assertDatabaseHas('accounting_sales', [
            'order_id' => $order->id,
            'amount' => 1000,
            'customer_id' => $customer->id,
            'payment_method' => 'card'
        ]);
    }

    /**
     * Test SyncOrderToAccounting does not record sale when not paid.
     */
    public function test_sync_order_to_accounting_does_not_record_sale_when_pending()
    {
        $order = Order::factory()->create([
            'payment_status' => PaymentStatus::PENDING,
        ]);

        $event = new PaymentStatusChanged($order);
        $listener = new SyncOrderToAccounting($this->accountingManager);

        $listener->handle($event);

        $this->assertDatabaseCount('accounting_sales', 0);
    }

    /**
     * Test SyncServiceOrderToAccounting listener.
     */
    public function test_sync_service_order_to_accounting_records_service_when_delivered()
    {
        $technician = User::factory()->create();
        $serviceOrder = ServiceOrder::factory()->create([
            'status' => ServiceOrderStatus::DELIVERED,
            'service_cost' => 500,
            'technician_id' => $technician->id
        ]);

        $event = new ServiceOrderStatusChanged($serviceOrder);
        $listener = new SyncServiceOrderToAccounting($this->accountingManager);

        $listener->handle($event);

        $this->assertDatabaseHas('accounting_services', [
            'service_order_id' => $serviceOrder->id,
            'amount' => 500,
            'technician_id' => $technician->id
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
