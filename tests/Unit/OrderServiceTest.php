<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Customer;
use App\Models\Device;
use App\Models\ServiceOrder;
use App\Services\OrderService;
use App\Services\SMSService;
use App\Jobs\SendSmsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\UserRoleSeeder::class);
        
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->user = $user;
        $this->actingAs($this->user);

        $this->orderService = app(OrderService::class);
    }

    #[Test]
    public function it_can_update_status_and_dispatch_event()
    {
        \Illuminate\Support\Facades\Event::fake();

        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'status' => 'registered',
            'receiver_phone' => '09123456789'
        ]);

        $updatedOrder = $this->orderService->updateStatus($serviceOrder, 'technician_assigned');
        $updatedOrder = $this->orderService->updateStatus($updatedOrder, 'repairing');

        $this->assertEquals('repairing', $updatedOrder->status->value);
        $this->assertNotNull($updatedOrder->repair_started_at);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\ServiceOrderStatusChanged::class, function ($event) use ($serviceOrder) {
            return $event->order->id === $serviceOrder->id;
        });
    }

    #[Test]
    public function it_can_create_a_service_order()
    {
        \Illuminate\Support\Facades\Event::fake();
        $customer = Customer::factory()->create();

        $data = [
            'customer_id' => $customer->id,
            'device_type' => 'Laptop',
            'device_model' => 'Dell XPS 15',
            'service_type' => 'in_company',
            'receiver_name' => 'John Doe',
            'receiver_phone' => '09123456789',
            'fault' => 'Screen broken',
        ];

        $order = $this->orderService->createOrder($data);

        $this->assertInstanceOf(ServiceOrder::class, $order);
        $this->assertEquals($customer->id, $order->customer_id);
        $this->assertEquals('registered', $order->status->value);
        $this->assertDatabaseHas('devices', [
            'type' => 'Laptop',
            'model' => 'Dell XPS 15',
        ]);
        
        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\ServiceOrderCreated::class);
    }

    #[Test]
    public function it_can_process_attachments()
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $order = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');
        $this->orderService->processAttachments([$file], $order);

        $this->assertDatabaseHas('attachments', [
            'attachable_id' => $order->id,
            'attachable_type' => ServiceOrder::class,
            'name' => 'test.jpg',
        ]);
        
        $attachment = \App\Models\Attachment::first();
        $this->assertNotNull($attachment);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists($attachment->path));
    }

    #[Test]
    public function it_throws_exception_for_invalid_transition()
    {
        $customer = Customer::factory()->create();
        $device = Device::factory()->create(['customer_id' => $customer->id]);
        $serviceOrder = ServiceOrder::factory()->create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'status' => 'registered',
        ]);

        $this->expectException(\InvalidArgumentException::class);

        // 'delivered' is not directly allowed from 'registered' (based on logic, actually it might be allowed if I check isValidStatusTransition implementation)
        // Let's check logic: registered -> [repairing, ready, delivered] ARE valid.
        // So registered -> delivered is valid.
        // delivered -> registered IS NOT valid.
        
        $serviceOrder->status = 'delivered';
        $serviceOrder->save();

        $this->orderService->updateStatus($serviceOrder, 'registered');
    }
}
