<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\AccountingSale;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderSettleDebtTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_order_debt_can_be_settled_from_order_show(): void
    {
        Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);

        $staff = User::factory()->create();
        $staff->assignRole('receptionist');

        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $staff->id,
            'status' => OrderStatus::DELIVERED,
            'payment_status' => PaymentStatus::PENDING,
            'payment_method' => 'debt',
            'shipping_method' => 'pickup',
            'total' => 70000000,
            'notes' => 'ثبت شده از طریق پنل فروش حضوری (POS)',
        ]);

        AccountingSale::create([
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'amount' => 70000000,
            'description' => 'فروش حضوری',
            'transaction_date' => now(),
            'payment_method' => 'debt',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($staff)->post(route('automation.orders.settle-debt', $order));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertSame(PaymentStatus::PAID, $order->payment_status);
        $this->assertSame('cancelled', AccountingSale::where('order_id', $order->id)->where('payment_method', 'debt')->value('status'));
        $this->assertTrue(
            AccountingSale::where('order_id', $order->id)
                ->where('status', 'completed')
                ->where('description', 'like', '%[پرداخت]%')
                ->exists()
        );
    }
}
