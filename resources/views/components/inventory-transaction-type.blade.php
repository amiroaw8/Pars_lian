@props(['type' => ''])

@php
    $normalized = strtolower(trim((string) $type));
    $labels = [
        'purchase' => ['label' => 'خرید', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'icon' => 'arrow-down-left'],
        'sale' => ['label' => 'فروش', 'class' => 'bg-rose-50 text-rose-800 border-rose-200', 'icon' => 'arrow-up-right'],
        'use' => ['label' => 'مصرف در تعمیر', 'class' => 'bg-amber-50 text-amber-900 border-amber-200', 'icon' => 'tools'],
        'return' => ['label' => 'برگشت به انبار', 'class' => 'bg-sky-50 text-sky-900 border-sky-200', 'icon' => 'arrow-back-up'],
        'adjustment' => ['label' => 'تعدیل موجودی', 'class' => 'bg-violet-50 text-violet-900 border-violet-200', 'icon' => 'adjustments'],
        'warranty_sent' => ['label' => 'ارسال گارانتی', 'class' => 'bg-orange-50 text-orange-900 border-orange-200', 'icon' => 'package-export'],
        'warranty_return' => ['label' => 'برگشت گارانتی', 'class' => 'bg-cyan-50 text-cyan-900 border-cyan-200', 'icon' => 'package-import'],
    ];
    $config = $labels[$normalized] ?? ['label' => $type ?: 'نامشخص', 'class' => 'bg-slate-50 text-slate-800 border-slate-200', 'icon' => 'help'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border ' . $config['class']]) }}>
    <i class="ti ti-{{ $config['icon'] }} text-sm"></i>
    {{ $config['label'] }}
</span>
