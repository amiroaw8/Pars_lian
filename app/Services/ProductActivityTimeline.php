<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\RepairItem;
use Illuminate\Support\Collection;

class ProductActivityTimeline
{
    /**
     * @return Collection<int, object{
     *   source: string,
     *   event_type: string,
     *   title: string,
     *   description: ?string,
     *   quantity_change: ?int,
     *   stock_after: ?int,
     *   occurred_at: \Carbon\Carbon,
     *   user_name: ?string,
     *   reference_type: ?string,
     *   reference_id: ?int
     * }>
     */
    public static function forProduct(Product $product, int $limit = 100): Collection
    {
        $stored = ProductActivity::query()
            ->where('product_id', $product->id)
            ->with('user:id,name')
            ->get()
            ->map(fn (ProductActivity $a) => (object) [
                'source' => 'stored',
                'event_type' => $a->event_type,
                'title' => $a->title,
                'description' => $a->description,
                'quantity_change' => $a->quantity_change,
                'stock_after' => $a->stock_after,
                'occurred_at' => $a->occurred_at,
                'user_name' => $a->user?->name,
                'reference_type' => $a->reference_type,
                'reference_id' => $a->reference_id,
            ]);

        $retro = self::retroactiveEntries($product);

        return $stored
            ->concat($retro)
            ->unique(fn ($row) => ($row->reference_type ?? 'x').':'.($row->reference_id ?? '0').':'.$row->event_type.':'.$row->occurred_at->timestamp)
            ->sortByDesc(fn ($row) => $row->occurred_at->timestamp)
            ->take($limit)
            ->values();
    }

    private static function retroactiveEntries(Product $product): Collection
    {
        $entries = collect();

        OrderItem::query()
            ->where('product_id', $product->id)
            ->with(['order:id,order_number,notes,created_at,user_id', 'order.user:id,name'])
            ->latest('id')
            ->limit(50)
            ->get()
            ->each(function (OrderItem $item) use ($entries) {
                $order = $item->order;
                if (! $order) {
                    return;
                }
                $isPos = str_contains((string) $order->notes, 'فروش حضوری') || str_contains((string) $order->notes, 'POS');
                $entries->push((object) [
                    'source' => 'retro',
                    'event_type' => $isPos ? 'shop_pos' : 'shop_online',
                    'title' => $isPos ? 'فروش حضوری' : 'فروش آنلاین',
                    'description' => "سفارش {$order->order_number} — {$item->quantity} عدد",
                    'quantity_change' => - (int) $item->quantity,
                    'stock_after' => null,
                    'occurred_at' => $order->created_at,
                    'user_name' => $order->user?->name,
                    'reference_type' => 'Order',
                    'reference_id' => $order->id,
                ]);
            });

        if ($product->inventory_id) {
            InventoryTransaction::query()
                ->where('inventory_id', $product->inventory_id)
                ->with('user:id,name')
                ->latest('id')
                ->limit(50)
                ->get()
                ->each(function (InventoryTransaction $tx) use ($entries) {
                    $type = match ($tx->transaction_type) {
                        'use', 'warranty_sent' => 'repair_use',
                        'sale' => str_contains((string) $tx->notes, 'فروش') ? 'shop_online' : 'inventory_adjust',
                        'return', 'warranty_return' => 'stock_return',
                        default => 'inventory_adjust',
                    };
                    $entries->push((object) [
                        'source' => 'retro',
                        'event_type' => $type,
                        'title' => $tx->notes ?: $tx->transaction_type,
                        'description' => "موجودی پس از تراکنش: {$tx->new_quantity}",
                        'quantity_change' => (int) $tx->quantity_change,
                        'stock_after' => (int) $tx->new_quantity,
                        'occurred_at' => $tx->created_at,
                        'user_name' => $tx->user?->name,
                        'reference_type' => 'InventoryTransaction',
                        'reference_id' => $tx->id,
                    ]);
                });

            RepairItem::query()
                ->where('inventory_id', $product->inventory_id)
                ->with(['serviceOrder:id', 'serviceOrder.customer:id,name'])
                ->latest('id')
                ->limit(50)
                ->get()
                ->each(function (RepairItem $item) use ($entries) {
                    $entries->push((object) [
                        'source' => 'retro',
                        'event_type' => 'repair_use',
                        'title' => 'قطعه در تعمیر',
                        'description' => 'سفارش تعمیر #'.$item->service_order_id
                            .($item->serviceOrder?->customer ? ' — '.$item->serviceOrder->customer->name : ''),
                        'quantity_change' => - (int) $item->quantity,
                        'stock_after' => null,
                        'occurred_at' => $item->created_at,
                        'user_name' => null,
                        'reference_type' => 'RepairItem',
                        'reference_id' => $item->id,
                    ]);
                });
        }

        return $entries;
    }
}
