<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\OrderShippingDefaults;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShippingDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_order_gets_default_city_when_customer_has_no_city(): void
    {
        $staff = User::factory()->create();
        $customer = Customer::factory()->create([
            'name' => 'صدرا حسینی',
            'phone' => '09121111111',
            'address' => 'خیابان شریعتی',
            'user_id' => null,
        ]);

        Product::factory()->create([
            'stock_quantity' => 5,
            'stock_status' => 'instock',
            'manage_stock' => true,
        ]);

        $defaults = OrderShippingDefaults::fromCustomer($customer);

        $this->assertSame('—', $defaults['shipping_city']);
        $this->assertSame('09121111111', $defaults['shipping_phone']);

        $order = Order::create(array_merge($defaults, [
            'user_id' => $staff->id,
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'shipping_method' => 'pickup',
            'subtotal' => 1000,
            'total' => 1000,
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'currency' => 'IRT',
        ]));

        $this->assertSame('—', $order->shipping_city);
    }
}
