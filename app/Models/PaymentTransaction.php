<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'transaction_id',
        'reference_id',
        'gateway',
        'status',
        'description',
        'payment_date',
        'gateway_response',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'gateway_response' => 'array',
        'amount' => 'decimal:0',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
