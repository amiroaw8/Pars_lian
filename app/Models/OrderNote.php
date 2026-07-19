<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderNote extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'visibility',
        'body',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isInternal(): bool
    {
        return $this->visibility === 'internal';
    }

    public function isCustomerVisible(): bool
    {
        return $this->visibility === 'customer';
    }
}
