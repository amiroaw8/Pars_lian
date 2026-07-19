<?php

namespace Tests\Feature;

use App\Models\AccountingSale;
use App\Models\AccountingService;
use App\Models\Attachment;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Models\OrderItem;
use App\Services\AccountDeletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_force_delete_customer_removes_orders_accounting_and_attachments(): void
    {
        Storage::fake('local');

        Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $staff = User::factory()->create();
        $staff->assignRole('receptionist');

        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
        ]);

        $path = 'attachments/test-file.pdf';
        Storage::disk('local')->put($path, 'content');

        Attachment::create([
            'name' => 'test-file.pdf',
            'path' => $path,
            'mime_type' => 'application/pdf',
            'size' => 7,
            'attachable_type' => ServiceOrder::class,
            'attachable_id' => $serviceOrder->id,
            'uploaded_by' => $staff->id,
        ]);

        AccountingService::create([
            'service_order_id' => $serviceOrder->id,
            'amount' => 1000000,
            'description' => '[بدهی] test',
            'transaction_date' => now(),
            'payment_status' => 'unpaid',
        ]);

        $shopOrder = Order::factory()->create([
            'user_id' => $staff->id,
            'shipping_phone' => $customer->phone,
            'total' => 2000000,
        ]);

        AccountingSale::create([
            'customer_id' => $customer->id,
            'order_id' => $shopOrder->id,
            'amount' => 2000000,
            'description' => '[بدهی] shop',
            'transaction_date' => now(),
            'payment_method' => 'debt',
            'status' => 'pending',
        ]);

        app(AccountDeletionService::class)->forceDeleteCustomer($customer);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
        $this->assertDatabaseMissing('service_orders', ['id' => $serviceOrder->id]);
        $this->assertDatabaseMissing('devices', ['id' => $device->id]);
        $this->assertDatabaseMissing('orders', ['id' => $shopOrder->id]);
        $this->assertDatabaseMissing('accounting_services', ['service_order_id' => $serviceOrder->id]);
        $this->assertDatabaseMissing('accounting_sales', ['customer_id' => $customer->id]);
        $this->assertDatabaseMissing('attachments', ['attachable_id' => $serviceOrder->id]);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_soft_delete_customer_does_not_error_when_order_items_removed(): void
    {
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'user_id' => User::factory()->create()->id,
            'shipping_phone' => $customer->phone,
        ]);

        OrderItem::factory()->create(['order_id' => $order->id]);

        app(AccountDeletionService::class)->softDeleteCustomer($customer);

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
        $this->assertSoftDeleted('orders', ['id' => $order->id]);
    }
}
