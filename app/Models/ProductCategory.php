<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 
        'parent_id', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function activeProducts(): HasMany
    {
        return $this->products()->where('is_active', true);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors & Mutators
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image ? asset('storage/'.$this->image) : null,
        );
    }

    // Methods
    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    public function getAllChildren(): Collection
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }

        return $children;
    }

    public function getFullPath()
    {
        $path = [$this->name];

        $parent = $this->parent;
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    public function depth(): int
    {
        $depth = 1;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    public function canHaveChildren(): bool
    {
        return $this->depth() < 3;
    }

    public function getDescendantIdsIncludingSelf(): array
    {
        $ids = [$this->id];
        $queue = [$this->id];

        while ($queue !== []) {
            $childIds = static::query()
                ->whereIn('parent_id', $queue)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->pluck('id')
                ->all();

            $queue = [];
            foreach ($childIds as $childId) {
                if (! in_array($childId, $ids, true)) {
                    $ids[] = $childId;
                    $queue[] = $childId;
                }
            }
        }

        return $ids;
    }

    public function productsInTreeCount(bool $activeOnly = false): int
    {
        $query = Product::query()
            ->whereIn('category_id', $this->getDescendantIdsIncludingSelf());

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return (int) $query->count();
    }

    public function treeProductsCount(): int
    {
        return $this->productsInTreeCount();
    }

    /** @return array<int, int> */
    public static function treeProductCountMap(): array
    {
        $countsByCategory = Product::query()
            ->selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id')
            ->map(fn ($count) => (int) $count)
            ->all();

        $childrenByParent = static::query()
            ->select('id', 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('parent_id');

        $memo = [];
        $resolve = function (int $id) use (&$resolve, &$memo, $countsByCategory, $childrenByParent): int {
            if (isset($memo[$id])) {
                return $memo[$id];
            }

            $total = (int) ($countsByCategory[$id] ?? 0);
            foreach ($childrenByParent[$id] ?? [] as $child) {
                $total += $resolve((int) $child->id);
            }

            return $memo[$id] = $total;
        };

        $map = [];
        foreach (static::query()->pluck('id') as $id) {
            $map[(int) $id] = $resolve((int) $id);
        }

        return $map;
    }

    public static function optionsForSelect(?int $excludeId = null): array
    {
        $options = [];
        $roots = static::with([
            'children' => fn ($q) => $q->ordered()->with([
                'children' => fn ($q2) => $q2->ordered(),
            ]),
        ])
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        $walk = function ($categories, int $depth = 0) use (&$walk, &$options, $excludeId) {
            foreach ($categories as $category) {
                if ($excludeId && (int) $category->id === $excludeId) {
                    continue;
                }

                $indent = $depth > 0 ? str_repeat('— ', $depth) : '';
                $options[$category->id] = $indent.$category->name;

                if ($category->relationLoaded('children') && $category->children->isNotEmpty()) {
                    $walk($category->children, $depth + 1);
                }
            }
        };

        $walk($roots);

        return $options;
    }

    /** @return array<int, string> */
    public static function optionPathsForSelect(?int $excludeId = null): array
    {
        $paths = [];
        $roots = static::with([
            'children' => fn ($q) => $q->ordered()->with([
                'children' => fn ($q2) => $q2->ordered(),
            ]),
        ])
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        $walk = function ($categories, string $prefix = '') use (&$walk, &$paths, $excludeId) {
            foreach ($categories as $category) {
                if ($excludeId && (int) $category->id === $excludeId) {
                    continue;
                }

                $path = $prefix === '' ? $category->name : $prefix.' / '.$category->name;
                $paths[$category->id] = $path;

                if ($category->relationLoaded('children') && $category->children->isNotEmpty()) {
                    $walk($category->children, $path);
                }
            }
        };

        $walk($roots);

        return $paths;
    }

    public static function expandSlugsToIds(array $slugs): array
    {
        return static::resolveFilterSelectionToCategoryIds($slugs);
    }

    /** @param  array<int|string>  $selection */
    public static function resolveFilterSelectionToCategoryIds(array $selection): array
    {
        $rootIds = [];

        foreach ($selection as $value) {
            if ($value === '' || $value === null) {
                continue;
            }

            if (ctype_digit((string) $value)) {
                $rootIds[] = (int) $value;

                continue;
            }

            foreach (static::where('slug', $value)->pluck('id') as $id) {
                $rootIds[] = (int) $id;
            }
        }

        $rootIds = array_values(array_unique($rootIds));
        if ($rootIds === []) {
            return [];
        }

        $ids = [];
        foreach (static::whereIn('id', $rootIds)->get() as $category) {
            $ids = array_merge($ids, $category->getDescendantIdsIncludingSelf());
        }

        return array_values(array_unique($ids));
    }

    /** @param  array<int|string>  $selection */
    public static function isSelectedInFilter(self $category, array $selection): bool
    {
        foreach ($selection as $value) {
            if ((string) $category->id === (string) $value || $category->slug === $value) {
                return true;
            }
        }

        return false;
    }

    /** @param  array<int|string>  $selection */
    public function branchHasSelectedInFilter(array $selection): bool
    {
        if (static::isSelectedInFilter($this, $selection)) {
            return true;
        }

        foreach ($this->children as $child) {
            if ($child->branchHasSelectedInFilter($selection)) {
                return true;
            }
        }

        return false;
    }

    /** @deprecated Use branchHasSelectedInFilter() */
    public function branchHasSelectedSlug(array $slugs): bool
    {
        return $this->branchHasSelectedInFilter($slugs);
    }
}
