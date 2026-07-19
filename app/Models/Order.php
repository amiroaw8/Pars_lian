<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Traits\CalculatesOrderTotals;
use App\Support\ShippingAddressPresenter;
use App\Support\OrderShippingDefaults;

class Order extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, CalculatesOrderTotals;

    protected $fillable = [
        'order_number', 'user_id', 'service_order_id', 'status', 'subtotal', 'tax_amount', 
        'shipping_amount', 'shipping_method', 'discount_amount', 'total', 'currency', 
        'payment_status', 'payment_method', 'notes', 'shipping_first_name', 
        'shipping_last_name', 'shipping_email', 'shipping_phone', 
        'shipping_address', 'shipping_city', 'shipping_state', 
        'shipping_postal_code', 'shipping_country', 'billing_first_name', 
        'billing_last_name', 'billing_email', 'billing_phone', 
        'billing_address', 'billing_city', 'billing_state', 
        'billing_postal_code', 'billing_country', 'shipped_at', 'delivered_at',
        'tracking_code', 'shipping_status', 'tracking_link',
    ];

    /**
     * Get the casts array.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Generate status message for SMS
     */
    public function getStatusSmsMessage(?string $statusId = null): string
    {
        $statusId = $statusId ?? $this->status->value;
        $statusModel = OrderStatusModel::find($statusId);
        
        if (!$statusModel || !$statusModel->sms_template) {
            return "وضعیت سفارش شما به {$statusModel?->label} تغییر یافت.";
        }

        return str_replace(
            ['{id}', '{tracking_code}'],
            [$this->order_number, (string) ($this->tracking_code ?? '')],
            $statusModel->sms_template
        );
    }

    /**
     * Generate payment status message for SMS
     */
    public function getPaymentStatusSmsMessage(?string $statusId = null): string
    {
        $statusId = $statusId ?? $this->payment_status->value;
        $statusModel = PaymentStatusModel::find($statusId);
        
        if (!$statusModel || !$statusModel->sms_template) {
            return "وضعیت پرداخت سفارش شما به {$statusModel?->label} تغییر یافت.";
        }

        return str_replace('{id}', $this->order_number, $statusModel->sms_template);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }

            if (! filled(trim((string) ($order->shipping_city ?? '')))) {
                $order->shipping_city = OrderShippingDefaults::requiredCity(null);
            }
        });

        static::saving(function ($order) {
            // Ensure total is always correct based on components
            $order->total = $order->subtotal + $order->tax_amount + $order->shipping_amount - $order->discount_amount;
        });

        static::saved(function ($order) {
            if ($order->wasChanged('payment_status') && $order->payment_status === PaymentStatus::PAID) {
                Cache::forget('accounting_totals');
                Cache::forget('accounting_totals_v6');
            }
        });
    }

    // Relationships
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatusModel::class, 'status', 'id');
    }

    public function paymentStatus(): BelongsTo
    {
        return $this->belongsTo(PaymentStatusModel::class, 'payment_status', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderNotes(): HasMany
    {
        return $this->hasMany(OrderNote::class)->latest();
    }

    public function internalNotes(): HasMany
    {
        return $this->hasMany(OrderNote::class)->where('visibility', 'internal')->latest();
    }

    public function customerNotes(): HasMany
    {
        return $this->hasMany(OrderNote::class)->where('visibility', 'customer')->latest();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Scopes
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::PENDING);
    }

    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::PROCESSING);
    }

    public function scopeShipped(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::SHIPPED);
    }

    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::DELIVERED);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::CANCELLED);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', PaymentStatus::PAID);
    }

    public function scopePosSales(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('notes', 'like', '%فروش حضوری%')
                ->orWhere('notes', 'like', '%POS%');
        });
    }

    public function scopeOnlineShop(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull('notes')
                    ->orWhere(function (Builder $q2) {
                        $q2->where('notes', 'not like', '%فروش حضوری%')
                            ->where('notes', 'not like', '%POS%');
                    });
            })
            ->whereIn('shipping_method', ['tipax', 'dekapost', 'snapp']);
    }

    public function isPosSale(): bool
    {
        $notes = (string) $this->notes;

        return str_contains($notes, 'فروش حضوری') || str_contains($notes, 'POS');
    }

    public function hasOutstandingDebt(): bool
    {
        return $this->payment_method === 'debt'
            && $this->payment_status === PaymentStatus::PENDING;
    }

    // Accessors & Mutators
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->status->label(),
        );
    }

    protected function paymentStatusLabel(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->payment_status->label(),
        );
    }

    protected function paymentMethodLabel(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => match ($this->payment_method) {
                'cod' => 'نقدی',
                'cash' => 'نقدی',
                'online' => 'پرداخت آنلاین',
                'card' => 'کارت به کارت',
                'debt' => 'بدهی',
                default => 'نامشخص'
            },
        );
    }

    protected function shippingMethodLabel(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => match ($this->shipping_method) {
                'tipax' => 'تیپاکس (پس‌کرایه)',
                'dekapost' => 'دکا پست (پس‌کرایه)',
                'snapp' => 'اسنپ (پس‌کرایه)',
                'pickup' => 'تحویل حضوری',
                default => 'نامشخص'
            },
        );
    }

    protected function shippingFullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => "{$this->shipping_first_name} {$this->shipping_last_name}",
        );
    }

    protected function billingFullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->billing_first_name 
                ? "{$this->billing_first_name} {$this->billing_last_name}"
                : "{$this->shipping_first_name} {$this->shipping_last_name}",
        );
    }

    protected function shippingFullAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => ShippingAddressPresenter::for($this)->singleLine(),
        );
    }

    // Methods
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(6));
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [OrderStatus::PENDING, OrderStatus::PROCESSING]);
    }

    public function canBeShipped(): bool
    {
        return $this->status === OrderStatus::PROCESSING;
    }

    public function canBeDelivered(): bool
    {
        return $this->status === OrderStatus::SHIPPED;
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'status' => OrderStatus::PROCESSING,
        ]);
    }

    public function markAsShipped(): void
    {
        $this->update([
            'status' => OrderStatus::SHIPPED,
            'shipped_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => OrderStatus::DELIVERED,
            'delivered_at' => now(),
        ]);
    }

    public function cancel(): bool
    {
        if ($this->canBeCancelled()) {
            return $this->update(['status' => OrderStatus::CANCELLED]);
        }

        return false;
    }

    public function calculateTotals()
    {
        $subtotal = $this->items->sum('total');
        
        // Consistent with Cart::calculateTotals logic via CalculatesOrderTotals trait
        $amounts = $this->calculateAmounts($subtotal);
        $tax = $amounts['tax_amount'];
        $shipping = $amounts['shipping_amount'];

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'shipping_amount' => $shipping,
            'total' => $subtotal + $tax + $shipping - $this->discount_amount,
        ]);
    }
}
