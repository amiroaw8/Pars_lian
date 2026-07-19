@props([
    'routePrefix' => 'automation.',
])

@php
    $items = \App\Support\AdminBreadcrumb::items();
    $dashboardLabel = 'میز کار';
@endphp

<div {{ $attributes->merge(['class' => 'breadcrumb-container animate-fade-in']) }}>
    <div class="breadcrumb-item">
        <a href="{{ route($routePrefix . 'dashboard') }}" class="breadcrumb-link" title="{{ $dashboardLabel }}">
            <i class="ti ti-home-2 text-lg" aria-hidden="true"></i>
            <span class="sr-only">{{ $dashboardLabel }}</span>
        </a>
    </div>

    @foreach($items as $item)
        @if($item['label'] === $dashboardLabel && count($items) === 1)
            @continue
        @endif
        <div class="breadcrumb-item">
            @if(! empty($item['url']))
                <a href="{{ url($item['url']) }}" class="breadcrumb-link">
                    @if($item['label'] instanceof \Illuminate\Support\HtmlString)
                        {!! $item['label'] !!}
                    @else
                        {{ $item['label'] }}
                    @endif
                </a>
            @else
                <span class="breadcrumb-current" aria-current="page">
                    @if($item['label'] instanceof \Illuminate\Support\HtmlString)
                        {!! $item['label'] !!}
                    @else
                        {{ $item['label'] }}
                    @endif
                </span>
            @endif
        </div>
    @endforeach
</div>
