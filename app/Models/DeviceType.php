<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'parent_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function countLoadedDescendants(): int
    {
        if (! $this->relationLoaded('children')) {
            return 0;
        }

        $count = 0;
        foreach ($this->children as $child) {
            $count += 1 + $child->countLoadedDescendants();
        }

        return $count;
    }

    /**
     * @return array<int, list<int>>
     */
    public static function childrenMap(): array
    {
        $map = [];
        foreach (static::query()->pluck('parent_id', 'id') as $id => $parentId) {
            if ($parentId === null) {
                continue;
            }
            $map[(int) $parentId][] = (int) $id;
        }

        return $map;
    }

    /**
     * @param  array<int, list<int>>|null  $childrenMap
     * @return list<int>
     */
    public static function descendantIdsFor(int $rootId, ?array $childrenMap = null): array
    {
        $childrenMap ??= static::childrenMap();

        $visited = [];
        $queue = [$rootId];
        $ids = [];

        while ($queue !== []) {
            $id = array_shift($queue);
            if (isset($visited[$id])) {
                continue;
            }

            $visited[$id] = true;
            $ids[] = $id;

            foreach ($childrenMap[$id] ?? [] as $childId) {
                if (! isset($visited[$childId])) {
                    $queue[] = $childId;
                }
            }
        }

        return $ids;
    }

    /**
     * @return list<int>
     */
    public function getDescendantIdsIncludingSelf(): array
    {
        return static::descendantIdsFor((int) $this->id);
    }

    /**
     * @return array<int, list<int>>
     */
    public static function parentExcludeMap(): array
    {
        $childrenMap = static::childrenMap();
        $map = [];

        foreach (static::query()->pluck('id') as $id) {
            $map[(int) $id] = static::descendantIdsFor((int) $id, $childrenMap);
        }

        return $map;
    }

    /**
     * Flat options for parent select: id => indented label.
     *
     * @return array<int, string>
     */
    public static function optionsForSelect(?int $excludeId = null): array
    {
        $excludeIds = $excludeId ? static::descendantIdsFor($excludeId) : [];
        $childrenMap = static::childrenMap();
        $names = static::query()->orderBy('name')->pluck('name', 'id');

        $options = [];

        $walk = function (int $parentId, string $prefix = '') use (&$walk, &$options, $childrenMap, $names, $excludeIds): void {
            foreach ($childrenMap[$parentId] ?? [] as $childId) {
                if (in_array($childId, $excludeIds, true)) {
                    continue;
                }

                $options[$childId] = $prefix.$names[$childId];
                $walk($childId, $prefix.'— ');
            }
        };

        foreach (static::query()->whereNull('parent_id')->orderBy('name')->pluck('id') as $rootId) {
            $rootId = (int) $rootId;
            if (in_array($rootId, $excludeIds, true)) {
                continue;
            }

            $options[$rootId] = $names[$rootId];
            $walk($rootId, '— ');
        }

        return $options;
    }
}
