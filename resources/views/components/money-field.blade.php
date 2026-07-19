@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => '',
    'required' => false,
    'placeholder' => 'مبلغ به تومان',
    'disabled' => false,
    'wordsId' => null,
])

@php
    use App\Support\ShopFormat;

    $inputId = $id ?? preg_replace('/[^a-z0-9]+/i', '-', $name);
    $raw = ShopFormat::toIntegerAmount($value);
    $wordsId = $wordsId ?? ('money-words-' . $inputId);
    $formatted = $raw > 0 ? ShopFormat::moneyAmount($raw) : '';
    $wordsText = $raw > 0 ? ShopFormat::amountInWords($raw) : '';
    $inputClass = 'bg-slate-50 border-none rounded-xl py-4 px-5 text-sm focus:ring-2 focus:ring-blue-500 transition-all font-black w-full ' . ($attributes->get('class') ?? '');
@endphp

<div class="money-field-wrap {{ $attributes->get('wrapperClass', '') }}">
    @if($label)
        <label for="{{ $inputId }}" class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">{{ $label }}</label>
    @endif
    <input
        type="text"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ $formatted }}"
        @if($raw > 0) data-raw-value="{{ $raw }}" @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        placeholder="{{ $placeholder }}"
        data-money-input
        data-money-words="#{{ $wordsId }}"
        data-money-words-url="{{ route('automation.money.words') }}"
        inputmode="numeric"
        autocomplete="off"
        class="{{ trim($inputClass) }}"
    />
    <p id="{{ $wordsId }}" class="money-input-words text-xs font-bold text-slate-500 mt-1 min-h-[1.25rem]">{{ $wordsText }}</p>
</div>
