<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;

/**
 * انبار منبع حقیقت موجودی است؛ محصولات متصل فقط بازتاب quantity انبار هستند.
 */
class ShopInventorySync
{
    public static function syncProductFromInventory(Product $product, bool $quiet = true): bool
    {
        if (! $product->inventory_id) {
            return false;
        }

        $inventory = Inventory::find($product->inventory_id);
        if (! $inventory) {
            return false;
        }

        $qty = (int) $inventory->quantity;
        $status = $qty > 0 ? 'instock' : 'outofstock';

        if ((int) $product->stock_quantity === $qty && $product->stock_status === $status) {
            return false;
        }

        $attributes = [
            'stock_quantity' => $qty,
            'stock_status' => $status,
            'manage_stock' => true,
        ];

        if ($quiet) {
            $product->fill($attributes)->saveQuietly();
        } else {
            $product->update($attributes);
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function applyInventoryMaster(array $data): array
    {
        if (empty($data['inventory_id'])) {
            return $data;
        }

        $inventory = Inventory::find($data['inventory_id']);
        if (! $inventory) {
            return $data;
        }

        $qty = (int) $inventory->quantity;
        $data['stock_quantity'] = $qty;
        $data['stock_status'] = $qty > 0 ? 'instock' : 'outofstock';
        $data['manage_stock'] = true;

        return $data;
    }

    /**
     * @return array{linked: int, synced: int, mismatches: list<array{product_id: int, product_qty: int, inventory_qty: int}>}
     */
    public static function reconcile(?int $inventoryId = null): array
    {
        $query = Product::query()->whereNotNull('inventory_id');
        if ($inventoryId !== null) {
            $query->where('inventory_id', $inventoryId);
        }

        $linked = 0;
        $synced = 0;
        $mismatches = [];

        foreach ($query->get() as $product) {
            $linked++;
            $inventory = Inventory::find($product->inventory_id);
            $invQty = $inventory ? (int) $inventory->quantity : null;

            if ($invQty !== null && (int) $product->stock_quantity !== $invQty) {
                $mismatches[] = [
                    'product_id' => $product->id,
                    'product_qty' => (int) $product->stock_quantity,
                    'inventory_qty' => $invQty,
                ];
            }

            if (self::syncProductFromInventory($product)) {
                $synced++;
            }
        }

        return [
            'linked' => $linked,
            'synced' => $synced,
            'mismatches' => $mismatches,
        ];
    }
}
