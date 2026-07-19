@props([
    'title',
    'subtitle' => null,
    'icon' => 'ti-smart-home',
    'badge' => null,
    'badgeIcon' => null,
    'actionUrl' => null,
    'actionText' => null,
    'actionIcon' => 'ti-plus',
    'headerIcon' => 'ti-layout-dashboard'
])

<div {{ $attributes->merge(['class' => 'admin-page-hero relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-black rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-slate-900/20 animate-fade-in group']) }}>
    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="text-center md:text-right">
            @if($badge)
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                <i class="ti {{ $badgeIcon ?? $icon }} text-primary-400"></i>
                {{ $badge }}
            </div>
            @endif
            <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">{{ $title }}</h2>
            @if($subtitle)
            <p class="text-slate-300 text-lg font-medium max-w-xl leading-relaxed">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="flex flex-col gap-4">
            <div class="w-24 h-24 md:w-40 md:h-40 bg-white/10 backdrop-blur-xl rounded-[2.5rem] flex items-center justify-center text-white border border-white/20 shadow-2xl animate-float group-hover:scale-110 transition-transform duration-500 mx-auto md:mr-auto">
                <i class="ti {{ $headerIcon }} text-6xl md:text-8xl drop-shadow-lg"></i>
            </div>
            @if(isset($extraActions))
                <div class="flex gap-2">
                    {{ $extraActions }}
                    @if($actionUrl && $actionText)
                    <a href="{{ $actionUrl }}" class="btn-modern btn-modern-primary py-4 px-8 shadow-xl shadow-primary-500/20 group">
                        <i class="ti {{ $actionIcon }} group-hover:rotate-90 transition-transform"></i>
                        <span>{{ $actionText }}</span>
                    </a>
                    @endif
                </div>
            @elseif($actionUrl && $actionText)
            <a href="{{ $actionUrl }}" class="btn-modern btn-modern-primary py-4 px-8 shadow-xl shadow-primary-500/20 group">
                <i class="ti {{ $actionIcon }} group-hover:rotate-90 transition-transform"></i>
                <span>{{ $actionText }}</span>
            </a>
            @endif
        </div>
    </div>
    
    <!-- Background Decorative Elements -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-primary-500/20 transition-colors duration-700"></div>
</div>
