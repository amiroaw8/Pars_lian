<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Support\InventoryTransactionNoteFormatter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryTransactionNoteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    public function test_repair_use_note_links_to_service_order(): void
    {
        $serviceOrder = ServiceOrder::factory()->create();

        $html = InventoryTransactionNoteFormatter::toHtml(
            "استفاده در تعمیر — سفارش تعمیر #{$serviceOrder->id}"
        );

        $this->assertStringContainsString(route('automation.repairs.show', $serviceOrder), $html);
        $this->assertStringContainsString('سفارش تعمیر #'.$serviceOrder->id, $html);
    }

    public function test_shop_pos_note_links_to_shop_order(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'ORD-20260627-TEST01',
        ]);

        $html = InventoryTransactionNoteFormatter::toHtml(
            "فروش حضوری محصول: پرینتر 2035 — سفارش {$order->order_number}"
        );

        $this->assertStringContainsString(route('automation.orders.show', $order), $html);
        $this->assertStringContainsString('سفارش '.$order->order_number, $html);
    }

    public function test_generic_service_order_hash_links_to_repair_page(): void
    {
        $serviceOrder = ServiceOrder::factory()->create();

        $html = InventoryTransactionNoteFormatter::toHtml(
            "اصلاحیه (افزایش) - سفارش #{$serviceOrder->id}"
        );

        $this->assertStringContainsString(route('automation.repairs.show', $serviceOrder), $html);
    }

    public function test_cardex_page_renders_jalali_date(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $inventory = Inventory::factory()->create();
        $inventory->updateStock(5, 'purchase', 'خرید تست');

        $response = $this->actingAs($user)->get(route('automation.inventory.reports.cardex', [
            'inventory_id' => $inventory->id,
        ]));

        $response->assertOk();
        $response->assertDontSee('2026/', false);
    }
}
