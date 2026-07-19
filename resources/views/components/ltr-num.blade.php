@props([
    'value' => null,
    'prefix' => '',
    'suffix' => '',
    'signed' => false,
    'format' => false,
])

@php
    if ($value !== null) {
        $numeric = (float) $value;
        $formatted = $format ? number_format($numeric) : (string) $value;

        if ($signed) {
            $text = ($numeric > 0 ? '+' : '').$formatted;
        } else {
            $text = $prefix.$formatted.$suffix;
        }
    } else {
        $text = $prefix.trim((string) $slot).$suffix;
    }
@endphp

<bdi dir="ltr" {{ $attributes->merge(['class' => 'ltr-num', 'translate' => 'no']) }}>{{ $text }}</bdi>
