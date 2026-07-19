<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'options',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'options' => 'array',
        ];
    }

    // Relationships
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    // Methods
    public function updateQuantity($quantity)
    {
        if ($quantity > 0 && $this->product->canBeOrdered($quantity)) {
            $this->update(['quantity' => $quantity]);
            $this->cart->calculateTotals();

            return true;
        }

        return false;
    }
}
