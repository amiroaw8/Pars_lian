<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['actions']));

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

foreach (array_filter((['actions']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="mb-8 animate-slide-up" style="animation-delay: 0.1s;">
    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
        <i class="ti ti-bolt text-primary-600"></i>
        دسترسی سریع
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="{{ $action['url'] }}" class="quick-action-tile quick-action-tile--default group">
                <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center mb-3 group-hover:bg-primary-50 group-hover:text-primary-600 transition-all">
                    <i class="ti ti-{{ $action['icon'] }} text-2xl"></i>
                </div>
                <span class="text-sm font-bold text-slate-700 group-hover:text-primary-600 transition-colors">{{ $action['label'] }}</span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
