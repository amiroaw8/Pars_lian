<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class AssignableTechnicians
{
    public const SETTING_KEY = 'service_assignable_technician_ids';

    /**
     * Staff users that super admin can pick from in settings.
     *
     * @return Collection<int, User>
     */
    public static function candidates(): Collection
    {
        return User::query()
            ->with('roles')
            ->where('is_active', true)
            ->whereHas('roles', function ($query): void {
                $query->whereIn('name', [
                    'super_admin',
                    'admin',
                    'technician',
                    'receptionist',
                    'warehouse',
                    'accountant',
                    'employee',
                ]);
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return list<int>
     */
    public static function configuredIds(): array
    {
        $raw = Setting::get(self::SETTING_KEY);
        if ($raw === null || $raw === '') {
            return [];
        }

        $decoded = json_decode((string) $raw, true);
        if (! is_array($decoded)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $decoded)));
    }

    /**
     * @return list<int>
     */
    public static function allowedIds(): array
    {
        $configured = self::configuredIds();
        if ($configured === []) {
            return User::role(['technician', 'admin', 'super_admin'])
                ->where('is_active', true)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return User::query()
            ->whereIn('id', $configured)
            ->where('is_active', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return Collection<int, User>
     */
    public static function forSelect(?int $alwaysIncludeUserId = null): Collection
    {
        $ids = self::allowedIds();
        $collection = $ids === []
            ? new Collection
            : User::query()
                ->whereIn('id', $ids)
                ->orderBy('name')
                ->get(['id', 'name']);

        if ($alwaysIncludeUserId && ! $collection->contains('id', $alwaysIncludeUserId)) {
            $extra = User::query()->whereKey($alwaysIncludeUserId)->first(['id', 'name']);
            if ($extra) {
                $collection = $collection->prepend($extra);
            }
        }

        return $collection;
    }

    public static function isAllowed(int $userId): bool
    {
        return in_array($userId, self::allowedIds(), true);
    }

    /**
     * @param  list<int>  $userIds
     */
    public static function saveConfiguredIds(array $userIds): void
    {
        $valid = User::query()
            ->whereIn('id', $userIds)
            ->where('is_active', true)
            ->whereHas('roles', function ($query): void {
                $query->whereIn('name', [
                    'super_admin',
                    'admin',
                    'technician',
                    'receptionist',
                    'warehouse',
                    'accountant',
                    'employee',
                ]);
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        Setting::set(self::SETTING_KEY, json_encode(array_values($valid)), [
            'group' => 'service',
            'label' => 'کاربران قابل انتخاب به‌عنوان تکنسین در پذیرش',
            'type' => 'json',
        ]);
    }
}
