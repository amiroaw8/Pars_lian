@if($src)
    @php
        $isHero = strpos($src, 'logo-hero') !== false;
        $mobileSrc = $isHero 
            ? asset('images/pars-lian-logo-hero-mobile.webp') . '?v=' . (file_exists(public_path('images/pars-lian-logo-hero-mobile.webp')) ? filemtime(public_path('images/pars-lian-logo-hero-mobile.webp')) : '')
            : asset('images/pars-lian-logo-mobile.webp') . '?v=' . (file_exists(public_path('images/pars-lian-logo-mobile.webp')) ? filemtime(public_path('images/pars-lian-logo-mobile.webp')) : '');
    @endphp
    <picture>
        <source srcset="{{ $mobileSrc }}" media="(max-width: 639px)">
        <img
            src="{{ $src }}"
            alt="پارس لیان — Pars Lian"
            {{ $imageAttributes() }}
        >
    </picture>
@endif
