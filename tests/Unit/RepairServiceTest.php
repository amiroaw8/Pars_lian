<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Customer;
use App\Models\Device;
use App\Models\ServiceOrder;
use App\Models\Inventory;
use App\Services\RepairService;
use App\Services\OrderService;
use App\Services\AccountingManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RepairServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $repairService;
    protected $orderService;
    protected $accountingManager;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\UserRoleSeeder::class);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->user = $user;
        $this->actingAs($this->user);

        $this->orderService = app(OrderService::class);
        $this->accountingManager = new AccountingManager();
        $this->repairService = new RepairService($this->orderService, $this->accountingManager);
    }

    #[Test]
    public function it_can_add_a_repair_item_and_update_stock()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $order = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
        ]);

        $inventory = Inventory::factory()->create([
            'quantity' => 10,
            'price' => 1000,
        ]);

        $data = [
            'name' => 'Screen',
            'item_type' => 'part',
            'inventory_id' => $inventory->id,
            'quantity' => 2,
            'cost' => 1200,
            'description' => 'Replacement screen',
        ];

        $item = $this->repairService->addItem($order, $data);

        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(8, $inventory->fresh()->quantity);
        $this->assertEquals(2400, $order->fresh()->service_cost);
    }

    #[Test]
    public function it_can_remove_a_repair_item_and_restore_stock()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $order = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
        ]);

        $inventory = Inventory::factory()->create([
            'quantity' => 8,
            'price' => 1000,
        ]);

        $item = $order->repairItems()->create([
            'name' => 'Screen',
            'item_type' => 'part',
            'inventory_id' => $inventory->id,
            'quantity' => 2,
            'cost' => 1200,
        ]);

        $this->repairService->removeItem($item);

        $this->assertEquals(10, $inventory->fresh()->quantity);
        $this->assertSoftDeleted('repair_items', ['id' => $item->id]);
        $this->assertEquals(0, $order->fresh()->service_cost);
    }

    #[Test]
    public function it_can_verify_payment_and_record_accounting()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $technician = User::factory()->create();
        $order = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'status' => 'accounting',
        ]);

        $item = $order->repairItems()->create([
            'name' => 'Labor',
            'item_type' => 'labor',
            'quantity' => 1,
            'cost' => 5000,
        ]);

        $this->repairService->verifyPayment($order, [$item->id => 5500]);

        $this->assertEquals('ready', $order->fresh()->status->value);
        $this->assertDatabaseCount('accounting_services', 2);
        $this->assertDatabaseHas('accounting_services', [
            'service_order_id' => $order->id,
            'amount' => 5500,
            'technician_id' => $technician->id,
            'description' => "[بدهی] هزینه تعمیر - سفارش #{$order->id}",
        ]);
        $this->assertDatabaseHas('accounting_services', [
            'service_order_id' => $order->id,
            'amount' => 5500,
            'description' => "[پرداخت] پرداخت هزینه تعمیر - سفارش #{$order->id}",
        ]);
    }
}
