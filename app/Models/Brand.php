<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'logo',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        });
    }

    public static function ensureFromCategoryTree(): void
    {
        if (Product::query()->active()->whereNotNull('brand_id')->exists()) {
            return;
        }

        $rootIds = ProductCategory::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->pluck('id');

        if ($rootIds->isEmpty()) {
            return;
        }

        ProductCategory::query()
            ->whereIn('parent_id', $rootIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->each(function (ProductCategory $category) {
                $brand = static::firstOrCreate(
                    ['slug' => $category->slug],
                    ['name' => $category->name, 'is_active' => true]
                );

                Product::query()
                    ->active()
                    ->whereIn('category_id', $category->getDescendantIdsIncludingSelf())
                    ->whereNull('brand_id')
                    ->update(['brand_id' => $brand->id]);
            });
    }

    /** @return Collection<int, static> */
    public static function forCatalogFilter(): Collection
    {
        static::ensureFromCategoryTree();

        return static::query()
            ->active()
            ->whereHas('products', fn (Builder $q) => $q->active())
            ->withCount(['products as products_count' => fn (Builder $q) => $q->active()])
            ->orderBy('name')
            ->get();
    }

    /** @param  array<int|string>  $selection */
    public static function isSelectedInFilter(self $brand, array $selection): bool
    {
        foreach ($selection as $value) {
            if ((string) $brand->id === (string) $value || $brand->slug === $value) {
                return true;
            }
        }

        return false;
    }

    /** @param  array<int|string>  $selection */
    public static function resolveFilterSelectionToIds(array $selection): array
    {
        $ids = [];

        foreach ($selection as $value) {
            if ($value === '' || $value === null) {
                continue;
            }

            if (ctype_digit((string) $value)) {
                $ids[] = (int) $value;

                continue;
            }

            foreach (static::where('slug', $value)->pluck('id') as $id) {
                $ids[] = (int) $id;
            }
        }

        return array_values(array_unique($ids));
    }
}
