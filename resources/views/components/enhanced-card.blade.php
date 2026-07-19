@props([
    'variant' => 'default',
    'animated' => true,
    'title' => null,
])

<div class="modern-card {{ $animated ? 'animate-fade-in' : '' }} mb-6">
    @if(isset($header) || $title)
        <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-slate-100);">
            @if($title)
                <h3 class="card-title" style="font-size: 1rem; font-weight: 700; color: var(--color-slate-800); margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                    {{ $title }}
                </h3>
            @else
                {{ $header }}
            @endif

            @if(isset($headerAction))
                <div class="card-header-action">
                    {{ $headerAction }}
                </div>
            @endif
        </div>
    @endif

    <div class="card-body" style="padding: 1.5rem;">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="card-footer" style="padding: 1.25rem 1.5rem; background-color: var(--color-slate-50); border-top: 1px solid var(--color-slate-100);">
            {{ $footer }}
        </div>
    @endif
</div>
