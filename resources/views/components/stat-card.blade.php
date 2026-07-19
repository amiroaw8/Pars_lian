<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'icon' => null,
    'tone' => 'primary',
    'variant' => 'row',
    'valueTitle' => null,
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
    'label',
    'icon' => null,
    'tone' => 'primary',
    'variant' => 'row',
    'valueTitle' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $iconTones = [
        'primary' => 'bg-primary-50 text-primary-600 group-hover:bg-primary-600 group-hover:text-white',
        'emerald' => 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white',
        'amber' => 'bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white',
        'blue' => 'bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white',
        'indigo' => 'bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white',
        'rose' => 'bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white',
        'purple' => 'bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white',
    ];
    $valueTones = [
        'primary' => 'text-slate-800',
        'emerald' => 'text-emerald-600',
        'amber' => 'text-amber-600',
        'blue' => 'text-blue-600',
        'indigo' => 'text-indigo-600',
        'rose' => 'text-rose-600',
        'purple' => 'text-purple-600',
    ];
    $iconClass = $iconTones[$tone] ?? $iconTones['primary'];
    $valueClass = $valueTones[$tone] ?? $valueTones['primary'];
    $baseClass = $variant === 'metric'
        ? 'stat-card-modern stat-card-modern--metric group'
        : 'stat-card-modern stat-card-modern--row group';
?>

<div {{ $attributes->merge(['class' => $baseClass]) }}>
    @if($variant === 'metric')
        @if(isset($decorIcon))
            <div class="pointer-events-none absolute -bottom-2 -right-2 opacity-50 transition-transform duration-500 group-hover:scale-110 text-slate-100">
                {{ $decorIcon }}

            </div>
        @endif
        <div class="relative z-10 min-w-0">
            <div class="stat-card-modern__value-lg {{ $valueClass }}" @if($valueTitle) title="{{ $valueTitle }}" @endif>
                {{ $slot }}

            </div>
            <div class="stat-card-modern__label-sm">{{ $label }}</div>
        </div>
    @else
        @if($icon)
        <div class="stat-card-modern__icon {{ $iconClass }} transition-all duration-300">
            <i class="ti {{ $icon }}"></i>
        </div>
        @endif
        <div class="stat-card-modern__content">
            <div class="stat-card-modern__label">{{ $label }}</div>
            <div class="stat-card-modern__value" @if($valueTitle) title="{{ $valueTitle }}" @endif>
                {{ $slot }}

            </div>
        </div>
    @endif
</div>
