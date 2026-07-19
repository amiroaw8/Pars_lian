<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'type',
        'condition',
        'quantity',
        'min_quantity',
        'price',
        'color',
        'description',
        'device_code',
        'rack_location',
        'compatibility_notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'min_quantity' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function repairItems(): HasMany
    {
        return $this->hasMany(RepairItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function scopeActive(Builder $query): void
    {
        // $query->where('is_active', true);
    }

    public function scopeInStock(Builder $query): void
    {
        $query->where('quantity', '>', 0);
    }

    protected static function booted()
    {
        static::created(function ($inventory) {
            if ($inventory->quantity > 0) {
                $inventory->transactions()->create([
                    'user_id' => Auth::id(),
                    'quantity_change' => $inventory->quantity,
                    'transaction_type' => 'purchase',
                    'notes' => 'ثبت موجودی اولیه هنگام ایجاد کالا',
                    'new_quantity' => $inventory->quantity,
                ]);
            }
        });

        static::updated(function ($inventory) {
            if ($inventory->wasChanged('quantity')) {
                foreach ($inventory->products as $product) {
                    \App\Services\ShopInventorySync::syncProductFromInventory($product);
                }
            }
        });
    }

    public function updateStock(int $quantityChange, string $transactionType, string $notes = '', array $details = []): bool
    {
        return DB::transaction(function () use ($quantityChange, $transactionType, $notes, $details) {
            // Refresh and lock for update to prevent race conditions
            $inventory = self::where('id', $this->id)->lockForUpdate()->first();

            $newQuantity = $inventory->quantity + $quantityChange;

            if ($newQuantity < 0) {
                throw new \RuntimeException(
                    "❌ خطا: موجودی کافی نیست! موجودی فعلی: {$inventory->quantity} - درخواست: " . abs($quantityChange)
                );
            }

            $inventory->quantity = $newQuantity;
            $inventory->save();

            foreach ($inventory->products()->get() as $linkedProduct) {
                \App\Services\ShopInventorySync::syncProductFromInventory($linkedProduct);
            }

            $transaction = $inventory->transactions()->create(array_merge([
                'user_id' => Auth::id(),
                'quantity_change' => $quantityChange,
                'transaction_type' => $transactionType,
                'notes' => $notes,
                'new_quantity' => $inventory->quantity,
            ], $details));

            // Sync the current instance
            $this->quantity = $inventory->quantity;

            $eventType = match ($transactionType) {
                'sale' => str_contains($notes, 'حضوری') ? 'shop_pos' : 'shop_online',
                'use', 'warranty_sent' => 'repair_use',
                'return', 'warranty_return' => 'stock_return',
                default => 'inventory_adjust',
            };

            if ($inventory->products()->exists()) {
                \App\Services\ProductActivityLogger::logForInventory(
                    $inventory->id,
                    $eventType,
                    $notes ?: $transactionType,
                    "موجودی انبار پس از تراکنش: {$inventory->quantity}",
                    $quantityChange,
                    null,
                    InventoryTransaction::class,
                    (int) $transaction->id,
                );
            }

            return true;
        });
    }
}
