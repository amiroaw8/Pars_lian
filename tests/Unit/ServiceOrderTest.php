<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Device;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ServiceOrderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_service_order()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);

        $serviceOrder = new ServiceOrder([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'service_type' => 'in_company',
            'receiver_name' => 'Test Receiver',
            'receiver_phone' => '09123456789',
            'fault' => 'Test fault description',
        ]);
        $serviceOrder->forceFill(['status' => 'registered']);
        $serviceOrder->save();

        $this->assertInstanceOf(ServiceOrder::class, $serviceOrder);
        $this->assertEquals('registered', $serviceOrder->status->value);
        $this->assertEquals('Test Receiver', $serviceOrder->receiver_name);
    }

    #[Test]
    public function it_belongs_to_customer()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
        ]);

        $this->assertInstanceOf(Customer::class, $serviceOrder->customer);
        $this->assertEquals($customer->id, $serviceOrder->customer->id);
    }

    #[Test]
    public function it_belongs_to_device()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
        ]);

        $this->assertInstanceOf(Device::class, $serviceOrder->device);
        $this->assertEquals($device->id, $serviceOrder->device->id);
    }

    #[Test]
    public function it_can_calculate_service_cost()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'service_cost' => 0,
        ]);

        // اضافه کردن آیتم‌های تعمیر
        $serviceOrder->repairItems()->createMany([
            ['item_type' => 'part', 'name' => 'Part 1', 'cost' => 100000, 'quantity' => 2],
            ['item_type' => 'labor', 'name' => 'Labor', 'cost' => 50000, 'quantity' => 1],
        ]);

        $serviceOrder->refresh();

        $this->assertEquals(250000, $serviceOrder->calculated_service_cost); // (100000*2) + 50000
    }

    #[Test]
    public function it_can_have_technician()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $technician = User::factory()->create(['name' => 'Test Technician']);

        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
        ]);

        $this->assertInstanceOf(User::class, $serviceOrder->technician);
        $this->assertEquals('Test Technician', $serviceOrder->technician->name);
    }
}
