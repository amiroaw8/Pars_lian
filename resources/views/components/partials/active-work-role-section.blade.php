@php
    $accent = $section['accent'] ?? '#64748b';
@endphp

<section
    id="active-work-{{ $section['key'] }}"
    class="modern-card overflow-hidden scroll-mt-28"
    style="border-top: 3px solid {{ $accent }};"
>
    <div class="card-header flex flex-wrap items-center justify-between gap-3" style="background: {{ $accent }}1a;">
        <h3 class="card-title text-base font-black text-slate-800 flex items-center gap-2 m-0">
            <i class="{{ $section['icon'] }} text-lg" style="color: {{ $accent }};"></i>
            {{ $section['label'] }}
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white border border-slate-200 text-slate-600">{{ $section['count'] }}</span>
        </h3>
        @if(! empty($section['cartable_url']))
            <a href="{{ $section['cartable_url'] }}" class="text-xs font-bold text-primary-600 hover:underline">مشاهده کارتابل</a>
        @endif
    </div>

    <div class="card-body p-4 space-y-2">
        @forelse($section['items'] as $item)
            @php
                $badge = match ($item['status_color'] ?? 'slate') {
                    'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'yellow', 'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'orange' => 'bg-orange-50 text-orange-700 border-orange-200',
                    'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'red', 'rose' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'green', 'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'teal' => 'bg-teal-50 text-teal-700 border-teal-200',
                    default => 'bg-slate-100 text-slate-600 border-slate-200',
                };
            @endphp
            <a href="{{ $item['url'] }}" class="flex items-start gap-4 p-4 rounded-2xl border border-slate-200 bg-white hover:border-primary-300 hover:shadow-md transition-all group">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $accent }}1a; color: {{ $accent }};">
                    <i class="ti ti-chevron-left text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-800 text-sm truncate group-hover:text-primary-700">{{ $item['title'] }}</div>
                    <div class="text-xs text-slate-500 mt-0.5 truncate">{{ $item['subtitle'] }}</div>
                </div>
                <div class="text-left shrink-0 space-y-1">
                    <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-black border {{ $badge }}">
                        {{ $item['status_label'] }}
                    </span>
                    <div class="text-[10px] text-slate-400 font-bold">{{ $item['updated_at_human'] }}</div>
                </div>
            </a>
        @empty
            <div class="p-6 text-center text-slate-400 text-xs font-bold">کار فعالی برای این نقش نیست.</div>
        @endforelse
    </div>
</section>
