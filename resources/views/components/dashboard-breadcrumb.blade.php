@props([
    'sections' => [],
])

<nav id="dashboard-breadcrumb" class="breadcrumb-container animate-fade-in" aria-label="مسیر میز کار">
    <div class="breadcrumb-item">
        <a href="{{ route('automation.dashboard') }}" class="breadcrumb-link">
            <i class="ti ti-layout-dashboard text-base" aria-hidden="true"></i>
            <span>میز کار</span>
        </a>
    </div>

    @forelse($sections as $section)
        <div class="breadcrumb-item">
            <a href="#active-work-{{ $section['key'] }}" class="breadcrumb-link">
                <i class="{{ $section['icon'] }} text-base" aria-hidden="true"></i>
                <span>{{ $section['label'] }}</span>
                @if(($section['count'] ?? 0) > 0)
                    <span class="active-work-breadcrumb-count inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-slate-800 text-white text-[10px] font-black" data-role-key="{{ $section['key'] }}">{{ $section['count'] }}</span>
                @else
                    <span class="active-work-breadcrumb-count hidden" data-role-key="{{ $section['key'] }}"></span>
                @endif
            </a>
        </div>
    @empty
        <div class="breadcrumb-item">
            <span class="breadcrumb-current" aria-current="page">{{ auth()->user()->getRoleDisplayName() }}</span>
        </div>
    @endforelse
</nav>
