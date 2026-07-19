@php
    use App\Support\BrandLogo;
    use App\Support\CompanyProfile;
@endphp
<div class="prt-header print-company-header">
    <div class="prt-brand">
        @if(BrandLogo::exists())
            <img src="{{ BrandLogo::dataUri() }}" alt="پارس لیان — Pars Lian" class="print-doc-logo">
        @endif
        <div>
            <p class="prt-sub">{{ CompanyProfile::SUBTITLE }}</p>
            <p class="prt-sub">
                <span class="prt-ltr">{{ CompanyProfile::PHONE }}</span>
                — <span class="prt-ltr">{{ CompanyProfile::WEBSITE }}</span>
            </p>
            <p class="prt-sub">{{ CompanyProfile::ADDRESS }}</p>
        </div>
    </div>
    @if(!empty($metaLines) || !empty($metaHtml))
    <div class="prt-meta">
        @foreach($metaLines ?? [] as $line)
            <div class="prt-badge">
                @if(!empty($line['label']))
                    <div class="prt-badge-label">{{ $line['label'] }}</div>
                @endif
                <div class="prt-badge-value">{{ $line['value'] }}</div>
            </div>
        @endforeach
        {!! $metaHtml ?? '' !!}
    </div>
    @endif
</div>
@if(!empty($docTitle))
<p class="prt-doc-title">{{ $docTitle }}</p>
@endif
