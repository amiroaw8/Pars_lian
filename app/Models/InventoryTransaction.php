<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class InventoryTransaction extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'inventory_id',
        'user_id',
        'quantity_change',
        'transaction_type',
        'notes',
        'new_quantity',
        'receiver',
        'organization',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'quantity_change' => 'integer',
            'new_quantity' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function setTransactionTypeAttribute(?string $value): void
    {
        $this->attributes['transaction_type'] = $value !== null ? strtolower(trim($value)) : null;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->transaction_type) {
            'purchase' => 'خرید',
            'sale' => 'فروش',
            'use' => 'مصرف در تعمیر',
            'return' => 'برگشت به انبار',
            'adjustment' => 'تعدیل موجودی',
            'warranty_sent' => 'ارسال گارانتی',
            'warranty_return' => 'برگشت گارانتی',
            default => (string) ($this->transaction_type ?: 'نامشخص'),
        };
    }
}
