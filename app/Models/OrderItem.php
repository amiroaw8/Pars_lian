<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'product_sku', 
        'quantity', 'price', 'total', 'product_options',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'total' => 'decimal:2',
            'product_options' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total = $item->price * $item->quantity;
        });

        static::saved(function ($item) {
            Order::withTrashed()->find($item->order_id)?->calculateTotals();
        });

        static::deleted(function ($item) {
            Order::withTrashed()->find($item->order_id)?->calculateTotals();
        });
    }

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // Accessors
    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price * $this->quantity,
        );
    }

    // Methods
    public function calculateTotal(): void
    {
        $this->total = $this->price * $this->quantity;
        $this->save();
    }
}
