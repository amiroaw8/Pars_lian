<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class RepairItem extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'service_order_id', 'inventory_id', 'item_type', 'name', 
        'description', 'cost', 'quantity', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'inventory_id' => 'integer',
            'cost' => 'decimal:2',
            'quantity' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($item) {
            ServiceOrder::withTrashed()->find($item->service_order_id)?->recalculateServiceCost();
        });

        static::deleted(function ($item) {
            ServiceOrder::withTrashed()->find($item->service_order_id)?->recalculateServiceCost();
        });
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function getItemNameAttribute(): string
    {
        return $this->name;
    }
}
