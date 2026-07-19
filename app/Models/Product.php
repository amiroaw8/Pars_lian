<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Inventory;

use App\Services\FileStorage;
use App\Traits\LogsActivity;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, Filterable;

    protected $searchable = ['name', 'description', 'sku'];

    protected $filterable = [
        'category_id' => null,
        'min_price' => null, // Will handle custom logic in scope
        'max_price' => null,
        'on_sale' => null,
        'featured' => null,
    ];

    protected $fillable = [
        'name', 'name_en', 'slug', 'external_url', 'description', 'short_description', 'sku', 'price', 
        'sale_price', 'stock_quantity', 'manage_stock', 'stock_status', 
        'images', 'category_id', 'brand_id', 'inventory_id', 'is_featured', 'is_new', 'is_active', 'weight', 
        'dimensions', 'attributes', 'technical_specs', 'view_count', 'sale_price_from', 'sale_price_to',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'manage_stock' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'is_active' => 'boolean',
            'weight' => 'decimal:2',
            'images' => 'array',
            'dimensions' => 'array',
            'attributes' => 'array',
            'technical_specs' => 'array',
            'view_count' => 'integer',
            'sale_price_from' => 'datetime',
            'sale_price_to' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saved(function (Product $product) {
            if ($product->wasChanged('inventory_id') && $product->inventory_id) {
                \App\Services\ShopInventorySync::syncProductFromInventory($product);
            }
        });
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ProductVersion::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProductActivity::class)->latest('occurred_at');
    }

    /**
     * Create a version snapshot of the current product state.
     */
    public function createVersion(?string $reason = null, ?int $userId = null): ProductVersion
    {
        return $this->versions()->create([
            'user_id' => $userId ?? Auth::id(),
            'data' => $this->toArray(),
            'change_reason' => $reason,
        ]);
    }


    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('is_new', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock_status', 'instock');
    }

    public function scopeOnSale(Builder $query): Builder
    {
        return $query->whereNotNull('sale_price')
            ->where('sale_price', '>', 0)
            ->where(function ($q) {
                $q->whereNull('sale_price_to')
                    ->orWhere('sale_price_to', '>', now());
            });
    }

    public function scopeByCategory(Builder $query, int|string $categoryIdOrSlug): Builder
    {
        $category = is_numeric($categoryIdOrSlug)
            ? ProductCategory::find((int) $categoryIdOrSlug)
            : ProductCategory::where('slug', $categoryIdOrSlug)->first();

        if (! $category) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('category_id', $category->getDescendantIdsIncludingSelf());
    }

    // Accessors & Mutators
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => [
                'name' => $value,
                'slug' => Str::slug($value),
            ],
        );
    }

    protected function mainImage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->images ? $this->images[0] : null,
        );
    }

    protected function resolveImageUrl(?string $image): string
    {
        if (! $image) {
            return asset('images/no-image.svg');
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            $path = parse_url($image, PHP_URL_PATH) ?? '';
            if ($path && (str_contains($path, '/storage/') || str_contains($path, 'storage/'))) {
                $relative = ltrim(preg_replace('#^.*/storage/#', '', $path), '/');

                return $this->publicStorageUrl($relative);
            }

            return $image;
        }

        $path = ltrim(str_replace('\\', '/', $image), '/');
        $path = preg_replace('#^storage/#', '', $path);

        return $this->publicStorageUrl($path);
    }

    protected function publicStorageUrl(string $path): string
    {
        return FileStorage::publicUrl($path) ?? asset('images/no-image.svg');
    }

    protected function mainImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resolveImageUrl($this->main_image),
        );
    }

    protected function allImageUrls(): Attribute
    {
        return Attribute::make(
            get: function () {
                return collect($this->images ?? [])->map(
                    fn ($image) => $this->resolveImageUrl($image)
                )->toArray();
            }
        );
    }

    protected function currentPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_on_sale && (float) $this->sale_price > 0) {
                    return (float) $this->sale_price;
                }

                return (float) ($this->price > 0 ? $this->price : $this->sale_price);
            },
        );
    }

    protected function isOnSale(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->sale_price || $this->sale_price <= 0) return false;
                if ($this->sale_price_to && $this->sale_price_to->isPast()) return false;
                return true;
            }
        );
    }

    protected function isInStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->manage_stock 
                ? $this->stock_quantity > 0 
                : $this->stock_status === 'instock',
        );
    }

    protected function stockDisplay(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->manage_stock) {
                    return match ($this->stock_status) {
                        'instock' => 'موجود',
                        'outofstock' => 'ناموجود',
                        'onbackorder' => 'قابل سفارش',
                        default => 'نامشخص'
                    };
                }

                if ($this->stock_quantity <= 0) return 'ناموجود';
                if ($this->stock_quantity <= 5) return "کم‌موجود ({$this->stock_quantity})";
                return "موجود ({$this->stock_quantity})";
            }
        );
    }

    // Methods
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function canBeOrdered(int $quantity = 1): bool
    {
        if (! $this->is_active) return false;

        if (! $this->manage_stock) {
            return $this->stock_status === 'instock';
        }

        return $this->stock_quantity >= $quantity;
    }

    public function isLinkedToInventory(): bool
    {
        return $this->inventory_id !== null;
    }

    public function reduceStock(int $quantity, string $saleChannel = 'shop_online', ?int $orderId = null): void
    {
        DB::transaction(function () use ($quantity, $saleChannel, $orderId) {
            $this->refresh();

            if ($this->isLinkedToInventory()) {
                $inventoryId = $this->inventory_id;
                $inventory = Inventory::where('id', $inventoryId)->lockForUpdate()->first();
                if (! $inventory) {
                    throw new \RuntimeException("انبار متصل به محصول {$this->name} یافت نشد.");
                }
                if ($inventory->quantity < $quantity) {
                    throw new \RuntimeException("موجودی انبار «{$inventory->name}» کافی نیست.");
                }

                $orderRef = '';
                if ($orderId) {
                    $order = \App\Models\Order::find($orderId);
                    $orderRef = $order?->order_number
                        ? " — سفارش {$order->order_number}"
                        : " — سفارش #{$orderId}";
                }
                $saleNote = ($saleChannel === 'shop_pos'
                    ? "فروش حضوری محصول: {$this->name}"
                    : "فروش آنلاین محصول: {$this->name}").$orderRef;

                $inventory->updateStock(-$quantity, 'sale', $saleNote);

                $this->refresh();
                if ($this->manage_stock && $this->stock_quantity <= 0) {
                    $this->updateQuietly(['stock_status' => 'outofstock']);
                }

                return;
            }

            if ($this->manage_stock) {
                if ($this->stock_quantity < $quantity) {
                    throw new \RuntimeException("موجودی محصول {$this->name} کافی نیست.");
                }

                $this->decrement('stock_quantity', $quantity);

                if ($this->stock_quantity <= 0) {
                    $this->update(['stock_status' => 'outofstock']);
                }

                $eventType = $saleChannel === 'shop_pos' ? 'shop_pos' : 'shop_online';
                \App\Services\ProductActivityLogger::logShopOnly(
                    $this->fresh(),
                    $eventType,
                    $eventType === 'shop_pos' ? 'فروش حضوری' : 'فروش آنلاین',
                    $orderId ? "سفارش #{$orderId}" : null,
                    -$quantity,
                    (int) $this->fresh()->stock_quantity,
                    $orderId ? 'Order' : null,
                    $orderId,
                );
            }
        });
    }

    public function restoreStock(int $quantity): void
    {
        DB::transaction(function () use ($quantity) {
            $this->refresh();

            if ($this->isLinkedToInventory()) {
                $inventory = Inventory::where('id', $this->inventory_id)->lockForUpdate()->first();
                if ($inventory) {
                    $inventory->updateStock($quantity, 'return', "لغو/برگشت محصول: {$this->name}");
                    $this->refresh();
                    if ($this->manage_stock && $this->stock_quantity > 0) {
                        $this->updateQuietly(['stock_status' => 'instock']);
                    }

                    return;
                }
            }

            if ($this->manage_stock) {
                $this->increment('stock_quantity', $quantity);

                if ($this->stock_quantity > 0) {
                    $this->update(['stock_status' => 'instock']);
                }

                \App\Services\ProductActivityLogger::logShopOnly(
                    $this->fresh(),
                    'stock_return',
                    'برگشت موجودی فروشگاه',
                    'محصول مستقل از انبار',
                    $quantity,
                    (int) $this->fresh()->stock_quantity,
                );
            }
        });
    }
}
