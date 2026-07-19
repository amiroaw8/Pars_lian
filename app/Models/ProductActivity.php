<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductActivity extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'event_type',
        'quantity_change',
        'stock_after',
        'reference_type',
        'reference_id',
        'title',
        'description',
        'meta',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity_change' => 'integer',
            'stock_after' => 'integer',
            'meta' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function eventLabel(): string
    {
        return match ($this->event_type) {
            'shop_online' => 'فروش آنلاین',
            'shop_pos' => 'فروش حضوری (POS)',
            'repair_use' => 'استفاده در تعمیر',
            'repair_return' => 'برگشت از تعمیر',
            'inventory_adjust' => 'تعدیل انبار',
            'stock_return' => 'برگشت موجودی',
            'inventory_linked' => 'اتصال به انبار',
            'inventory_unlinked' => 'قطع اتصال انبار',
            'product_edit' => 'ویرایش محصول',
            'out_of_stock' => 'اتمام موجودی',
            default => $this->event_type,
        };
    }

    public function badgeClass(): string
    {
        return match ($this->event_type) {
            'shop_online', 'shop_pos' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'repair_use' => 'bg-amber-50 text-amber-700 border-amber-200',
            'repair_return', 'stock_return' => 'bg-blue-50 text-blue-700 border-blue-200',
            'inventory_linked', 'inventory_unlinked' => 'bg-violet-50 text-violet-700 border-violet-200',
            'inventory_adjust', 'out_of_stock' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
