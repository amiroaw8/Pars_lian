@props(['value', 'hash' => true])

<bdi dir="ltr" {{ $attributes->merge(['class' => 'hash-ref', 'translate' => 'no']) }}>@if($hash)#@endif{{ $value }}</bdi>
