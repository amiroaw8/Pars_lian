<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['size' => 'md', 'mode' => 'web']));

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

foreach (array_filter((['size' => 'md', 'mode' => 'web']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use App\Support\BrandLogo;

    $heights = [
        'sm' => 'h-9',
        'md' => 'h-11',
        'lg' => 'h-14',
        'xl' => 'h-16',
        'print' => 'h-[52px]',
        'admin' => 'h-9',
    ];
    $maxWidths = [
        'sm' => 'max-w-[200px]',
        'md' => 'max-w-[240px]',
        'lg' => 'max-w-[280px]',
        'xl' => 'max-w-[320px]',
        'print' => 'max-w-[240px]',
        'admin' => 'max-w-[140px]',
    ];
    $heightClass = $heights[$size] ?? $heights['md'];
    $maxWidthClass = $maxWidths[$size] ?? $maxWidths['md'];
    $src = $mode === 'print' ? BrandLogo::dataUri() : BrandLogo::url();
?>

@if($src)
    <img
        src="{{ $src }}"
        alt="پارس لیان — Pars Lian"
        {{ $attributes->merge(['class' => $heightClass . ' w-auto ' . $maxWidthClass . ' rounded-xl object-contain object-right']) }}

        loading="{{ $mode === 'web' ? 'eager' : 'lazy' }}"
        decoding="async"
    >
@endif
