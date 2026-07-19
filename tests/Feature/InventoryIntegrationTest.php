<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup user
        $this->user = User::factory()->create();
    }

    public function test_checkout_deducts_inventory_stock()
    {
        // 1. Arrange
        $inventory = Inventory::factory()->create([
            'quantity' => 10,
        ]);

        $product = Product::factory()->create([
            'inventory_id' => $inventory->id,
            'manage_stock' => true,
            'stock_quantity' => 10,
            'price' => 10000,
        ]);

        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->addItem($product->id, 2);

        // 2. Act
        $response = $this->actingAs($this->user)
            ->post(route('checkout.store'), [
                'payment_method' => 'cod',
                'shipping_method' => 'pickup',
                'shipping_first_name' => 'John',
                'shipping_last_name' => 'Doe',
                'shipping_phone' => '09123456789',
                'shipping_address' => 'Test Address',
                'shipping_city' => 'Tehran',
                'shipping_state' => 'Tehran',
            ]);

        // 3. Assert
        $response->assertRedirect();
        
        $inventory->refresh();
        $product->refresh();

        $this->assertEquals(8, $inventory->quantity, 'Inventory quantity should be deducted');
        $this->assertEquals(8, $product->stock_quantity, 'Product stock quantity should be deducted');
        
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_id' => $inventory->id,
            'quantity_change' => -2,
            'transaction_type' => 'sale',
        ]);
    }

    public function test_checkout_fails_if_insufficient_stock()
    {
        // 1. Arrange
        $inventory = Inventory::factory()->create([
            'quantity' => 1,
        ]);

        $product = Product::factory()->create([
            'inventory_id' => $inventory->id,
            'manage_stock' => true,
            'stock_quantity' => 1,
            'price' => 10000,
        ]);

        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->addItem($product->id, 2); // Requesting 2, only 1 available

        // 2. Act
        $response = $this->actingAs($this->user)
            ->post(route('checkout.store'), [
                'payment_method' => 'cod',
                'shipping_method' => 'pickup',
                'shipping_first_name' => 'John',
                'shipping_last_name' => 'Doe',
                'shipping_phone' => '09123456789',
                'shipping_address' => 'Test Address',
                'shipping_city' => 'Tehran',
                'shipping_state' => 'Tehran',
            ]);

        // 3. Assert
        $response->assertSessionHas('error');
        
        $inventory->refresh();
        $this->assertEquals(1, $inventory->quantity, 'Inventory should not change on failed order');
    }

    public function test_order_cancellation_restores_inventory_stock()
    {
        // 1. Arrange
        $inventory = Inventory::factory()->create([
            'quantity' => 10,
        ]);

        $product = Product::factory()->create([
            'inventory_id' => $inventory->id,
            'manage_stock' => true,
            'stock_quantity' => 10,
            'price' => 10000,
        ]);

        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->addItem($product->id, 2);

        // Place order
        $this->actingAs($this->user)
            ->post(route('checkout.store'), [
                'payment_method' => 'cod',
                'shipping_method' => 'pickup',
                'shipping_first_name' => 'John',
                'shipping_last_name' => 'Doe',
                'shipping_phone' => '09123456789',
                'shipping_address' => 'Test Address',
                'shipping_city' => 'Tehran',
                'shipping_state' => 'Tehran',
            ]);

        $order = \App\Models\Order::latest()->first();
        
        // Verify initial deduction
        $inventory->refresh();
        $this->assertEquals(8, $inventory->quantity, 'Initial stock should be 8');

        // 2. Act - Cancel Order
        $order->update(['status' => \App\Enums\OrderStatus::CANCELLED]);

        // 3. Assert
        $inventory->refresh();
        $product->refresh();

        $this->assertEquals(10, $inventory->quantity, 'Inventory quantity should be restored to 10');
        $this->assertEquals(10, $product->stock_quantity, 'Product stock quantity should be restored to 10');
        
        $this->assertDatabaseHas('inventory_transactions', [
            'inventory_id' => $inventory->id,
            'quantity_change' => 2,
            'transaction_type' => 'return',
        ]);
    }
}
