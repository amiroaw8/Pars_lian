@if($src)
    @php
        $isHero = strpos($src, 'logo-hero') !== false;
        $mobileSrc = $isHero 
            ? asset('images/pars-lian-logo-hero-mobile.webp') . '?v=' . (file_exists(public_path('images/pars-lian-logo-hero-mobile.webp')) ? filemtime(public_path('images/pars-lian-logo-hero-mobile.webp')) : '')
            : asset('images/pars-lian-logo-mobile.webp') . '?v=' . (file_exists(public_path('images/pars-lian-logo-mobile.webp')) ? filemtime(public_path('images/pars-lian-logo-mobile.webp')) : '');
        
        $sizes = $isHero ? '(max-width: 639px) 320px, 518px' : '(max-width: 639px) 150px, 320px';
    @endphp
    <img
        src="{{ $src }}"
        srcset="{{ $mobileSrc }} 639w, {{ $src }} 1000w"
        sizes="{{ $sizes }}"
        alt="پارس لیان — Pars Lian"
        {{ $imageAttributes() }}
    >
@endif
