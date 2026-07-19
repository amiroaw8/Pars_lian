<?php

namespace App\Support;

use Illuminate\Support\Str;

class AdminBreadcrumb
{
    /** @var array<string, string> */
    private const LABELS = [
        'activity-logs' => 'لاگ فعالیت‌ها',
        'categories' => 'دسته‌بندی محصولات',
        'customers' => 'مشتریان',
        'dashboard' => 'میز کار',
        'devices' => 'دستگاه‌ها',
        'device-types' => 'انواع دستگاه',
        'files' => 'مدیریت فایل‌ها',
        'inventory' => 'انبار',
        'orders' => 'سفارشات فروشگاه',
        'pos' => 'فروش حضوری',
        'products' => 'محصولات',
        'recycle-bin' => 'سطل زباله',
        'repairs' => 'تعمیرات',
        'service-orders' => 'پذیرش‌ها',
        'settings' => 'تنظیمات',
        'sms' => 'پیامک',
        'users' => 'کاربران',
        'accounting' => 'مالی',
        'sales' => 'فروش‌ها',
        'expenses' => 'هزینه‌ها',
        'proforma' => 'پیش‌فاکتور',
        'reports' => 'گزارش‌ها',
        'balance' => 'تراز انبار',
        'cardex' => 'کاردکس',
        'transactions' => 'گردش کالا',
        'logs' => 'گزارش پیامک‌ها',
        'print' => 'چاپ',
        'brands' => 'برندها',
        'create' => 'ایجاد',
        'edit' => 'ویرایش',
        'history' => 'تاریخچه',
        'tracking' => 'پیگیری',
        'show' => 'جزئیات',
    ];

    /** @return list<array{label: string, url: string|null, current: bool}> */
    public static function items(): array
    {
        $segments = request()->segments();

        if (isset($segments[0]) && in_array($segments[0], ['automation', 'panel', 'system-admin', 'admin'], true)) {
            array_shift($segments);
        }

        if ($segments === []) {
            return [];
        }

        $areaPrefix = self::areaPrefix();
        $items = [];
        $accumulated = $areaPrefix;

        foreach ($segments as $index => $segment) {
            $accumulated .= '/'.$segment;
            $isLast = $index === count($segments) - 1;

            if (is_numeric($segment)) {
                $items[] = [
                    'label' => HashRef::html($segment),
                    'url' => $isLast ? null : $accumulated,
                    'current' => $isLast,
                ];

                continue;
            }

            if ($segment === 'show') {
                continue;
            }

            $items[] = [
                'label' => self::labelFor($segment),
                'url' => $isLast ? null : self::linkFor($accumulated, $segment, $segments, $index),
                'current' => $isLast,
            ];
        }

        return $items;
    }

    private static function areaPrefix(): string
    {
        $path = request()->path();

        if (str_starts_with($path, 'panel/')) {
            return '/panel';
        }

        if (str_starts_with($path, 'automation/')) {
            return '/automation';
        }

        if (str_starts_with($path, 'system-admin/')) {
            return '/system-admin';
        }

        if (str_starts_with($path, 'admin/')) {
            return '/admin';
        }

        return '';
    }

    private static function labelFor(string $segment): string
    {
        return self::LABELS[$segment] ?? Str::headline(str_replace('-', ' ', $segment));
    }

    /** @param  list<string>  $segments */
    private static function linkFor(string $accumulated, string $segment, array $segments, int $index): string
    {
        if (in_array($segment, ['create', 'edit', 'history'], true) && $index > 0) {
            $resourcePath = self::areaPrefix();
            for ($i = 0; $i < $index; $i++) {
                $resourcePath .= '/'.$segments[$i];
            }

            return $resourcePath;
        }

        return $accumulated;
    }
}
