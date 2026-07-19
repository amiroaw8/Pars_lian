<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Device;
use App\Models\ServiceOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_customer()
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'phone' => '09123456789',
            'address' => 'Test Address',
        ]);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('Test Customer', $customer->name);
        $this->assertEquals('09123456789', $customer->phone);
    }

    #[Test]
    public function phone_must_be_unique()
    {
        Customer::create([
            'name' => 'Customer 1',
            'phone' => '09123456789',
            'address' => 'Address 1',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Customer::create([
            'name' => 'Customer 2',
            'phone' => '09123456789', // تلفن تکراری
            'address' => 'Address 2',
        ]);
    }

    #[Test]
    public function it_can_have_multiple_devices()
    {
        $customer = Customer::factory()->create();

        $devices = Device::factory()->count(3)->create([
            'customer_id' => $customer->id,
        ]);

        $this->assertCount(3, $customer->devices);
        $this->assertInstanceOf(Device::class, $customer->devices->first());
    }

    #[Test]
    public function it_can_have_multiple_service_orders()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);

        $serviceOrders = ServiceOrder::factory()->count(2)->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
        ]);

        $this->assertCount(2, $customer->serviceOrders);
        $this->assertInstanceOf(ServiceOrder::class, $customer->serviceOrders->first());
    }

    #[Test]
    public function it_can_be_searched_by_name_or_phone()
    {
        Customer::factory()->create(['name' => 'John Doe', 'phone' => '09111111111']);
        Customer::factory()->create(['name' => 'Jane Smith', 'phone' => '09222222222']);

        $resultsByName = Customer::where('name', 'like', '%John%')->get();
        $resultsByPhone = Customer::where('phone', 'like', '%2222%')->get();

        $this->assertCount(1, $resultsByName);
        $this->assertCount(1, $resultsByPhone);
        $this->assertEquals('John Doe', $resultsByName->first()->name);
        $this->assertEquals('09222222222', $resultsByPhone->first()->phone);
    }

    #[Test]
    public function address_can_be_nullable()
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'phone' => '09333333333',
            // address is optional
        ]);

        $this->assertNull($customer->address);
    }
}
