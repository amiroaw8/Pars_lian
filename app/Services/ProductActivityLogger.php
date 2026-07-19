<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductActivity;
use Illuminate\Support\Facades\Auth;

class ProductActivityLogger
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public static function log(
        Product $product,
        string $eventType,
        string $title,
        ?string $description = null,
        ?int $quantityChange = null,
        ?int $stockAfter = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        array $meta = [],
        ?\DateTimeInterface $occurredAt = null,
    ): ProductActivity {
        $product->refresh();

        return ProductActivity::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'event_type' => $eventType,
            'quantity_change' => $quantityChange,
            'stock_after' => $stockAfter ?? (int) $product->stock_quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'title' => $title,
            'description' => $description,
            'meta' => $meta ?: null,
            'occurred_at' => $occurredAt ?? now(),
        ]);
    }

    public static function logForInventory(
        int $inventoryId,
        string $eventType,
        string $title,
        ?string $description = null,
        ?int $quantityChange = null,
        ?int $stockAfter = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        array $meta = [],
    ): void {
        $products = Product::where('inventory_id', $inventoryId)->get();

        foreach ($products as $product) {
            $product->refresh();
            $shopQty = (int) $product->stock_quantity;

            self::log(
                $product,
                $eventType,
                $title,
                trim(($description ? $description.' — ' : '')."موجودی فروشگاه: {$shopQty}"),
                $quantityChange,
                $stockAfter ?? $shopQty,
                $referenceType,
                $referenceId,
                array_merge($meta, ['inventory_id' => $inventoryId, 'logged_via' => 'warehouse_sync']),
            );
        }
    }

    /**
     * ثبت تغییر موجودی فقط در تاریخچه محصول (محصول مستقل از انبار).
     */
    public static function logShopOnly(
        Product $product,
        string $eventType,
        string $title,
        ?string $description = null,
        ?int $quantityChange = null,
        ?int $stockAfter = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        array $meta = [],
    ): ProductActivity {
        return self::log(
            $product,
            $eventType,
            $title,
            $description,
            $quantityChange,
            $stockAfter,
            $referenceType,
            $referenceId,
            array_merge($meta, ['logged_via' => 'shop_only']),
        );
    }
}
