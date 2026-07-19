<?php

declare(strict_types=1);

namespace App\Support;

class RoleLabels
{
    /** @var array<string, array{label: string, icon: string}> */
    private const ROLES = [
        'super_admin' => ['label' => 'سوپر ادمین', 'icon' => 'ti-crown'],
        'admin' => ['label' => 'مدیر سیستم', 'icon' => 'ti-shield-check'],
        'technician' => ['label' => 'تعمیرکار', 'icon' => 'ti-tools'],
        'receptionist' => ['label' => 'پذیرش', 'icon' => 'ti-headset'],
        'warehouse' => ['label' => 'انباردار', 'icon' => 'ti-packages'],
        'accountant' => ['label' => 'حسابدار', 'icon' => 'ti-calculator'],
        'customer' => ['label' => 'مشتری', 'icon' => 'ti-user'],
        'employee' => ['label' => 'کارمند', 'icon' => 'ti-id-badge'],
    ];

    /** @var array<string, string> */
    private const ACCENTS = [
        'super_admin' => '#7c3aed',
        'admin' => '#2563eb',
        'technician' => '#d97706',
        'receptionist' => '#059669',
        'warehouse' => '#ea580c',
        'accountant' => '#e11d48',
        'customer' => '#64748b',
        'employee' => '#475569',
    ];

    public static function label(string $roleName): string
    {
        return self::ROLES[$roleName]['label'] ?? str_replace('_', ' ', $roleName);
    }

    public static function icon(string $roleName): string
    {
        return self::ROLES[$roleName]['icon'] ?? 'ti-user-cog';
    }

    public static function accent(string $roleName): string
    {
        return self::ACCENTS[$roleName] ?? '#64748b';
    }

    /** @return array{label: string, icon: string} */
    public static function meta(string $roleName): array
    {
        return [
            'label' => self::label($roleName),
            'icon' => self::icon($roleName),
        ];
    }
}
