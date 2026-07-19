<?php

namespace Tests\Feature\Automation;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesRoles;
use Tests\TestCase;

class PosCheckoutTest extends TestCase
{
    use CreatesRoles;
    use RefreshDatabase;

    public function test_pos_checkout_deducts_linked_inventory_stock(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = Customer::factory()->create();

        $category = ProductCategory::factory()->create();
        $inventory = Inventory::factory()->create([
            'quantity' => 20,
            'price' => 50000,
        ]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'inventory_id' => $inventory->id,
            'stock_quantity' => 20,
            'manage_stock' => true,
            'price' => 100000,
            'is_active' => true,
        ]);

        $response = $this->actingAsStaff($admin)->postJson(route('automation.pos.checkout'), [
            'customer_id' => $customer->id,
            'items' => [
                ['id' => $product->id, 'quantity' => 3],
            ],
            'payment_method' => 'cod',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['order_id', 'order_number']);

        $inventory->refresh();
        $product->refresh();

        $this->assertSame(17, $inventory->quantity);
        $this->assertSame(17, $product->stock_quantity);
    }

    public function test_pos_checkout_fails_when_stock_is_insufficient(): void
    {
        $admin = $this->createUserWithRole('admin');
        $customer = Customer::factory()->create();

        $product = Product::factory()->create([
            'stock_quantity' => 1,
            'manage_stock' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAsStaff($admin)->postJson(route('automation.pos.checkout'), [
            'customer_id' => $customer->id,
            'items' => [
                ['id' => $product->id, 'quantity' => 5],
            ],
            'payment_method' => 'cod',
        ]);

        $response->assertStatus(422);
        $product->refresh();
        $this->assertSame(1, $product->stock_quantity);
    }
}
