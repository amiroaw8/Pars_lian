<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'label' => null,
    'value' => '',
    'required' => false,
    'placeholder' => 'مبلغ به تومان',
    'class' => '',
    'wordsId' => null,
    'wordsClass' => 'text-xs font-bold text-slate-500 min-h-[1.25rem] mt-1',
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
    'name',
    'label' => null,
    'value' => '',
    'required' => false,
    'placeholder' => 'مبلغ به تومان',
    'class' => '',
    'wordsId' => null,
    'wordsClass' => 'text-xs font-bold text-slate-500 min-h-[1.25rem] mt-1',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use App\Support\ShopFormat;

    $raw = ShopFormat::toIntegerAmount($value);
    $wordsId = $wordsId ?? ('money-words-' . preg_replace('/[^a-z0-9]+/i', '-', $name));
    $formatted = $raw > 0 ? ShopFormat::moneyAmount($raw) : '';
    $wordsText = $raw > 0 ? ShopFormat::amountInWords($raw) : '';
?>

<div class="money-input-wrap space-y-1.5 {{ $attributes->get('wrapperClass', '') }}">
    @if($label)
        <label for="{{ $wordsId }}-input" class="form-field-label block mb-2">{{ $label }}</label>
    @endif
    <input
        type="text"
        inputmode="numeric"
        name="{{ $name }}"
        id="{{ $wordsId }}-input"
        value="{{ $formatted }}"
        @if($raw > 0) data-raw-value="{{ $raw }}" @endif
        @if($required) required @endif
        placeholder="{{ $placeholder }}"
        data-money-input
        data-money-words="#{{ $wordsId }}"
        data-money-words-url="{{ route('automation.money.words') }}"
        autocomplete="off"
        {{ $attributes->except(['wrapperClass', 'class', 'wordsId', 'wordsClass'])->merge(['class' => 'form-control w-full money-input-field ' . $class]) }}

    />
    <p id="{{ $wordsId }}" class="{{ $wordsClass }}">{{ $wordsText }}</p>
</div>
