@props([
    'value' => null,
    'format' => 'Y/m/d H:i',
    'timezone' => 'Asia/Tehran',
    'fallback' => '—',
])

{{ jalali_date($value, $format, $timezone) ?? $fallback }}
