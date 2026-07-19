<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

use App\Traits\CalculatesOrderTotals;

/**
 * @property float $subtotal
 * @property float $tax_amount
 * @property float $shipping_amount
 * @property float|null $discount_amount
 * @property float $total
 */
class Cart extends Model
{
    use HasFactory, CalculatesOrderTotals;

    protected $fillable = [
        'session_id',
        'user_id',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total',
        'coupon_code',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeBySession(Builder $query, ?string $sessionId = null): Builder
    {
        $sessionId = $sessionId ?: Session::getId();
        return $query->active()->where('session_id', $sessionId);
    }

    public function scopeByUser(Builder $query, ?int $userId = null): Builder
    {
        $userId = $userId ?: Auth::id();
        return $query->active()->where('user_id', $userId);
    }

    // Methods
    public static function getCurrentCart(): self
    {
        $query = static::with(['items.product'])->active();

        $cart = Auth::check()
            ? $query->byUser()->first()
            : $query->bySession()->first();

        if (!$cart) {
            $cart = static::create([
                'session_id' => Auth::check() ? null : Session::getId(),
                'user_id' => Auth::id(),
                'subtotal' => 0,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'discount_amount' => 0,
                'total' => 0,
                'expires_at' => now()->addDays(7), // Cart expires in 7 days
                'is_active' => true,
            ]);
            $cart->load(['items.product']);
        }

        return $cart;
    }

    public function addItem(int $productId, int $quantity = 1, array $options = []): bool
    {
        $product = Product::find($productId);

        if (!$product || !$product->is_active) {
            return false;
        }

        /** @var CartItem|null $cartItem */
        $cartItem = $this->items()->where('product_id', $productId)->first();
        $newQuantity = $cartItem ? $cartItem->quantity + $quantity : $quantity;

        if (!$product->canBeOrdered($newQuantity)) {
            return false;
        }

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQuantity,
                'price' => $product->current_price, // Update to latest price
            ]);
        } else {
            $this->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product->current_price,
                'options' => $options,
            ]);
        }

        $this->refresh();
        $this->calculateTotals();

        return true;
    }

    public function updateItemQuantity(int $productId, int $quantity): bool
    {
        $product = Product::find($productId);
        if (!$product)
            return false;

        /** @var CartItem|null $cartItem */
        $cartItem = $this->items()->where('product_id', $productId)->first();

        if (!$cartItem) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }

        if ($product->canBeOrdered($quantity)) {
            $cartItem->update([
                'quantity' => $quantity,
                'price' => $product->current_price // Sync price on update
            ]);
            $this->refresh();
            $this->calculateTotals();
            return true;
        }

        return false;
    }

    public function removeItem(int $productId): bool
    {
        $this->items()->where('product_id', $productId)->delete();
        $this->calculateTotals();

        return true;
    }

    public function calculateTotals(): void
    {
        // Load items to ensure we have the latest data
        $this->load('items');

        $subtotal = $this->items->sum(fn($item) => $item->price * $item->quantity);

        // Consistent with Order::calculateTotals logic via CalculatesOrderTotals trait
        $amounts = $this->calculateAmounts($subtotal);
        $tax = $amounts['tax_amount'];
        $shipping = $amounts['shipping_amount'];

        $this->subtotal = $subtotal;
        $this->tax_amount = $tax;
        $this->shipping_amount = $shipping;
        $this->total = $subtotal + $tax + $shipping - ($this->discount_amount ?? 0);

        $this->save();
    }

    protected function itemCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->items->sum('quantity'),
        );
    }

    public function convertToOrder(array $orderData): Order
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($orderData) {
            // Create order from cart
            $order = Order::create(array_merge($orderData, [
                'user_id' => $this->user_id,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'shipping_amount' => $this->shipping_amount,
                'discount_amount' => $this->discount_amount,
                'total' => $this->total,
            ]));

            // Convert cart items to order items
            foreach ($this->items as $cartItem) {
                // Lock the product for update to prevent race conditions
                $product = Product::lockForUpdate()->find($cartItem->product_id);

                if (!$product) {
                    throw new \Exception("محصول با شناسه {$cartItem->product_id} یافت نشد.");
                }

                if (!$product->is_active) {
                    throw new \Exception("محصول {$product->name} دیگر فعال نیست.");
                }

                // Lock connected inventory row if exists to prevent DB race conditions
                if ($product->inventory_id) {
                    \App\Models\Inventory::where('id', $product->inventory_id)->lockForUpdate()->first();
                }

                // Check stock safely
                if (!$product->canBeOrdered($cartItem->quantity)) {
                    throw new \Exception("موجودی محصول {$product->name} کافی نیست.");
                }

                // Reduce stock (handles both local stock and inventory sync)
                $product->reduceStock($cartItem->quantity, 'shop_online', $order->id);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->price * $cartItem->quantity,
                    'product_options' => $cartItem->options,
                ]);
            }

            // Clear cart
            $this->items()->delete();
            $this->delete();

            return $order;
        });
    }

    public function mergeWithUserCart($userId)
    {
        $userCart = static::where('user_id', $userId)->first();

        if ($userCart && $userCart->id !== $this->id) {
            // Merge items from session cart to user cart
            foreach ($this->items as $item) {
                /** @var CartItem|null $existingItem */
                $existingItem = $userCart->items()->where('product_id', $item->product_id)->first();

                if ($existingItem) {
                    $totalRequested = $existingItem->quantity + $item->quantity;
                    $product = $item->product;

                    if ($product && $product->canBeOrdered($totalRequested)) {
                        $existingItem->quantity = $totalRequested;
                    } else if ($product) {
                        $existingItem->quantity = $product->manage_stock ? $product->stock_quantity : $totalRequested;
                    }
                    $existingItem->save();
                } else {
                    $item->update(['cart_id' => $userCart->id]);
                }
            }

            $this->delete();
            $userCart->calculateTotals();

            return $userCart;
        }

        // Convert session cart to user cart
        $this->update(['user_id' => $userId, 'session_id' => null]);

        return $this;
    }
}
