<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed necessary data or create user
        $this->user = User::factory()->create();
    }

    public function test_user_can_initiate_payment()
    {
        // 1. Arrange
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'payment_method' => 'online',
            'subtotal' => 100000,
            'total' => 100000,
            'order_number' => 'ORD-12345',
        ]);

        // Mock the Payment Gateway
        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('request')
            ->once()
            ->with(
                Mockery::on(function ($arg) use ($order) {
                    return $arg->id === $order->id;
                }),
                100000,
                Mockery::any() // callback URL
            )
            ->andReturn([
                'success' => true,
                'transaction_id' => 'AUTH-123456',
                'payment_url' => 'https://sandbox.zarinpal.com/pg/StartPay/AUTH-123456',
            ]);

        $this->app->instance(PaymentGatewayInterface::class, $mockGateway);

        // 2. Act
        $response = $this->actingAs($this->user)
            ->get(route('payment.pay', $order));

        // 3. Assert
        $response->assertRedirect('https://sandbox.zarinpal.com/pg/StartPay/AUTH-123456');

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'amount' => 100000,
            'status' => 'pending',
            'transaction_id' => 'AUTH-123456',
        ]);
    }

    public function test_payment_callback_success()
    {
        // 1. Arrange
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'payment_method' => 'online',
            'total' => 100000,
        ]);

        // Create initial transaction
        $order->transactions()->create([
            'amount' => 100000,
            'gateway' => 'zarinpal',
            'status' => 'pending',
            'transaction_id' => 'AUTH-123456',
            'description' => 'Test Payment',
        ]);

        // Mock the Payment Gateway
        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('verify')
            ->once()
            ->with(
                Mockery::on(function ($arg) use ($order) {
                    return $arg->id === $order->id;
                }),
                100000,
                'AUTH-123456'
            )
            ->andReturn([
                'success' => true,
                'reference_id' => 'REF-987654',
                'card_pan' => '6037********1234',
            ]);

        $this->app->instance(PaymentGatewayInterface::class, $mockGateway);

        // 2. Act
        $response = $this->actingAs($this->user)
            ->get(route('payment.callback', [
                'order' => $order->id,
                'Authority' => 'AUTH-123456',
                'Status' => 'OK',
            ]));

        // 3. Assert
        $response->assertRedirect(route('checkout.success', $order->order_number));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::PROCESSING,
            'payment_status' => PaymentStatus::PAID,
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'transaction_id' => 'AUTH-123456',
            'status' => 'success',
            'reference_id' => 'REF-987654',
        ]);
    }

    public function test_payment_callback_failed()
    {
        // 1. Arrange
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'payment_method' => 'online',
            'total' => 100000,
        ]);

        // Create initial transaction
        $order->transactions()->create([
            'amount' => 100000,
            'gateway' => 'zarinpal',
            'status' => 'pending',
            'transaction_id' => 'AUTH-123456',
            'description' => 'Test Payment',
        ]);

        // Mock Gateway - verify should NOT be called if Status is NOK
        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldNotReceive('verify');
        
        $this->app->instance(PaymentGatewayInterface::class, $mockGateway);

        // 2. Act
        $response = $this->actingAs($this->user)
            ->get(route('payment.callback', [
                'order' => $order->id,
                'Authority' => 'AUTH-123456',
                'Status' => 'NOK',
            ]));

        // 3. Assert
        $response->assertRedirect(route('checkout.success', $order->order_number));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => PaymentStatus::FAILED,
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'transaction_id' => 'AUTH-123456',
            'status' => 'failed',
        ]);
    }
}
