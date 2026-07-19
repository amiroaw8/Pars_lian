@php
    use App\Support\BrandLogo;
    use App\Support\CompanyProfile;
@endphp
<div class="prt-header">
    <div class="prt-brand">
        @if(BrandLogo::exists())
            <img src="{{ BrandLogo::dataUri() }}" alt="پارس لیان — Pars Lian" class="print-doc-logo">
        @endif
        <div>
            <p class="prt-sub">{{ CompanyProfile::SUBTITLE }}</p>
            <p class="prt-sub">
                <span class="prt-ltr">{{ CompanyProfile::PHONE }}</span>
                — {{ CompanyProfile::ADDRESS }}
            </p>
        </div>
    </div>
    <div class="prt-meta">
        <div class="prt-badge">
            <div class="prt-badge-label">شماره سفارش</div>
            <div class="prt-badge-value"><x-hash-ref :value="$serviceOrder->id" /></div>
        </div>
    </div>
</div>
@if(!empty($docTitle))
<p class="prt-doc-title">{{ $docTitle }}</p>
@endif
