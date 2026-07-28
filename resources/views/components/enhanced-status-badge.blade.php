<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'status' => 'unknown',
    'label' => null,
    'variant' => 'auto',
    'animated' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'status' => 'unknown',
    'label' => null,
    'variant' => 'auto',
    'animated' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // تعریف وضعیت‌ها و variantهای مربوطه
    $statusConfig = [
        // وضعیت‌های سفارش فروشگاهی
        'pending' => [
            'label' => 'در انتظار بررسی',
            'variant' => 'warning',
            'icon' => 'clock'
        ],
        'processing' => [
            'label' => 'در حال پردازش',
            'variant' => 'info-solid',
            'icon' => 'loader'
        ],
        'shipped' => [
            'label' => 'ارسال شده',
            'variant' => 'indigo-solid',
            'icon' => 'truck'
        ],
        // وضعیت‌های سفارش تعمیر
        'registered' => [
            'label' => 'ثبت شده',
            'variant' => 'info-solid',
            'icon' => 'clipboard-list'
        ],
        'repairing' => [
            'label' => 'در حال تعمیر', 
            'variant' => 'warning-solid',
            'icon' => 'tool'
        ],
        'ready' => [
            'label' => 'آماده تحویل',
            'variant' => 'success-solid',
            'icon' => 'circle-check'
        ],
        'delivered' => [
            'label' => 'تحویل شده',
            'variant' => 'primary-solid',
            'icon' => 'truck-delivery'
        ],
        'technician_assigned' => [
            'label' => 'تکنسین تخصیص یافته',
            'variant' => 'indigo-solid',
            'icon' => 'user-check'
        ],
        'rejected' => [
            'label' => 'غیر قابل تعمیر',
            'variant' => 'danger',
            'icon' => 'ban'
        ],
        'accounting' => [
            'label' => 'در انتظار حسابداری',
            'variant' => 'accounting-highlight',
            'icon' => 'calculator'
        ],
        'pending_parts' => [
            'label' => 'منتظر قطعه',
            'variant' => 'warning',
            'icon' => 'package'
        ],
        'sent_to_workshop' => [
            'label' => 'ارسال به کارگاه',
            'variant' => 'indigo',
            'icon' => 'truck'
        ],
        'archived' => [
            'label' => 'بایگانی شده',
            'variant' => 'secondary',
            'icon' => 'archive'
        ],
        'cancelled' => [
            'label' => 'لغو شده',
            'variant' => 'danger',
            'icon' => 'x'
        ],
        // نقش‌های کاربری
        'admin' => [
            'label' => 'مدیر',
            'variant' => 'primary',
            'icon' => 'user-shield'
        ],
        'super_admin' => [
            'label' => 'مدیر کل',
            'variant' => 'danger',
            'icon' => 'shield-lock'
        ],
        'technician' => [
            'label' => 'تعمیرکار',
            'variant' => 'warning',
            'icon' => 'tool'
        ],
        'receptionist' => [
            'label' => 'پذیرش',
            'variant' => 'info',
            'icon' => 'headset'
        ],
        'warehouse' => [
            'label' => 'انباردار',
            'variant' => 'indigo',
            'icon' => 'building-warehouse'
        ],
        'accountant' => [
            'label' => 'حسابدار',
            'variant' => 'success',
            'icon' => 'receipt-tax'
        ],
        'customer' => [
            'label' => 'مشتری',
            'variant' => 'secondary',
            'icon' => 'user-heart'
        ],
        'user' => [
            'label' => 'کاربر عادی',
            'variant' => 'secondary',
            'icon' => 'user'
        ],
        'unknown' => [
            'label' => 'نامشخص',
            'variant' => 'secondary',
            'icon' => 'help'
        ]
    ];
    
    // اگر وضعیت در آرایه نبود، سعی می‌کنیم از label ورودی استفاده کنیم
    $config = $statusConfig[$status] ?? [
        'label' => $label ?? ($statusConfig[$status]['label'] ?? 'نامشخص'),
        'variant' => $variant !== 'auto' ? $variant : 'secondary',
        'icon' => 'help'
    ];

    // نگاشت واریانت‌ها به کلاس‌های Tailwind برای وضوح بیشتر
    $variantClasses = [
        'primary' => 'bg-blue-50 text-blue-700 border-blue-200',
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'danger' => 'bg-rose-50 text-rose-700 border-rose-200',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'info' => 'bg-sky-50 text-sky-700 border-sky-200',
        'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'indigo-solid' => 'bg-indigo-600 text-white border-indigo-700 shadow-md ring-1 ring-indigo-400/30',
        'accounting' => 'bg-orange-100 text-orange-950 border-2 border-orange-500 shadow-sm',
        'accounting-solid' => 'bg-orange-600 text-white border-orange-700 shadow-md ring-1 ring-orange-400/40',
        'accounting-highlight' => 'bg-orange-50 text-orange-950 border-2 border-orange-500 shadow-sm ring-1 ring-orange-200',
        'info-solid' => 'bg-sky-600 text-white border-sky-700 shadow-md ring-1 ring-sky-500/30',
        'primary-solid' => 'bg-blue-600 text-white border-blue-700 shadow-md ring-1 ring-blue-500/30',
        'success-solid' => 'bg-emerald-600 text-white border-emerald-700 shadow-md ring-1 ring-emerald-500/30',
        'warning-solid' => 'bg-amber-500 text-white border-amber-600 shadow-md ring-1 ring-amber-400/30',
        'secondary' => 'bg-slate-50 text-slate-700 border-slate-200',
        // Fallback for direct color usage if needed
        'bg-secondary' => 'bg-slate-50 text-slate-700 border-slate-200',
        'bg-info' => 'bg-sky-50 text-sky-700 border-sky-200',
        'bg-success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'bg-danger' => 'bg-rose-50 text-rose-700 border-rose-200',
    ];

    $solidVariants = ['success-solid', 'warning-solid', 'accounting-solid', 'info-solid', 'primary-solid', 'indigo-solid'];
    $resolvedVariant = $variant !== 'auto' ? $variant : ($config['variant'] ?? 'secondary');
    $isSolidBadge = in_array($resolvedVariant, $solidVariants, true);
    $currentVariantClass = $variantClasses[$variant] ?? ($variantClasses[$config['variant']] ?? $variantClasses['secondary']);
    
    $displayLabel = $label ?? $config['label'];
    $isAccountingBadge = $status === 'accounting' || $resolvedVariant === 'accounting-highlight';
    $badgeClass = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold border ' . $currentVariantClass;
    if ($isAccountingBadge) {
        $badgeClass .= ' status-badge-accounting whitespace-nowrap text-xs sm:text-sm leading-snug';
    }
    if ($isSolidBadge) {
        $badgeClass .= ' status-badge-solid';
    }
    
    if ($animated) {
        $badgeClass .= ' animate-pulse';
    }

    $solidStyle = 'color:#ffffff;-webkit-text-fill-color:#ffffff;background-clip:padding-box;-webkit-background-clip:padding-box;';
    $solidChildStyle = 'color:#ffffff;-webkit-text-fill-color:#ffffff;';
?>

@if($isSolidBadge)
<span {{ $attributes->merge(['class' => $badgeClass, 'style' => $solidStyle]) }}>
    <i class="ti ti-{{ $config['icon'] }} text-sm status-badge-solid-icon" style="{{ $solidChildStyle }}"></i>
    <span class="status-badge-solid-text" style="{{ $solidChildStyle }}">{{ $displayLabel }}</span>
</span>
@else
<span {{ $attributes->merge(['class' => $badgeClass]) }}>
    <i class="ti ti-{{ $config['icon'] }} text-sm"></i>
    <span>{{ $displayLabel }}</span>
</span>
@endif
