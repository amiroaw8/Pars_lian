<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['order']));

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

foreach (array_filter((['order']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $presenter = \App\Support\ShippingAddressPresenter::for($order);
    $lines = $presenter->lines();
    $postalCode = $presenter->postalCode();
?>

<div {{ $attributes->merge(['class' => 'font-bold text-slate-800 leading-relaxed']) }}>
    @if($lines === [])
        <div class="text-slate-500 font-medium">آدرسی ثبت نشده است</div>
    @else
        <?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>{{ $line }}</div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    @endif

    @if($postalCode)
        <div class="text-xs text-slate-500 font-medium mt-1">کد پستی: {{ $postalCode }}</div>
    @endif
</div>
