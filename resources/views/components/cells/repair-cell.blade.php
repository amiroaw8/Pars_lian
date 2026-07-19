<div class="space-y-6 cell-membrane animate-fade-in" data-cell="repair">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
            <div class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/20">
                <i class="ti ti-tool"></i>
            </div>
            بخش تعمیرات و سرویس
        </h2>
        <a href="{{ auth()->user()->isTechnician() ? route('automation.repairs.index', ['view' => 'my_repairs']) : route('automation.service-orders.create') }}" class="btn-modern btn-modern-primary py-2 px-4 text-xs">
            <i class="ti ti-{{ auth()->user()->isTechnician() ? 'list' : 'plus' }}"></i>
            {{ auth()->user()->isTechnician() ? 'لیست تعمیرات' : 'پذیرش جدید' }}
        </a>
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="stat-card-modern bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-2 -bottom-2 text-blue-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                <i class="ti ti-clipboard-list text-6xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-2xl font-black text-slate-800 mb-1">
                    {{ $stats['primary_value'] }}
                </div>
                <div class="text-[11px] font-bold text-slate-500">
                    {{ $stats['primary_label'] }}
                </div>
            </div>
        </div>
        <div class="stat-card-modern bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-2 -bottom-2 text-emerald-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                <i class="ti ti-check text-6xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-2xl font-black text-slate-800 mb-1">
                    {{ $stats['secondary_value'] }}
                </div>
                <div class="text-[11px] font-bold text-slate-500">
                    {{ $stats['secondary_label'] }}
                </div>
            </div>
        </div>
    </div>

    <x-enhanced-card title="آخرین پذیرش‌ها" icon="ti-clipboard-list" animated>
        <x-slot name="headerAction">
            <form action="{{ route('automation.dashboard') }}" method="GET" class="relative flex items-center gap-2">
                @if(request('sales_search'))
                    <input type="hidden" name="sales_search" value="{{ request('sales_search') }}">
                @endif
                <input type="text" name="repair_search" value="{{ request('repair_search') }}" 
                       class="form-control-modern py-1.5 px-3 pr-8 text-[10px] w-48" 
                       placeholder="جستجوی تعمیرات...">
                <i class="ti ti-search absolute right-2 top-1/2 -translate-y-1/2 text-slate-400"></i>
                @if(request('repair_search'))
                    <a href="{{ route('automation.dashboard', ['sales_search' => request('sales_search')]) }}" class="text-rose-500 hover:text-rose-700">
                        <i class="ti ti-x"></i>
                    </a>
                @endif
            </form>
        </x-slot>
        <div class="overflow-x-auto -mx-6">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-[10px] font-bold">
                        <th class="px-6 py-4">کد</th>
                        <th class="px-6 py-4">مشتری</th>
                        <th class="px-6 py-4">دستگاه</th>
                        <th class="px-6 py-4">وضعیت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentRepairs as $repair)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('automation.service-orders.show', $repair) }}" class="font-bold text-blue-600"><x-hash-ref :value="$repair->id" /></a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-slate-700">{{ $repair->customer->name ?? 'نامشخص' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-[10px] text-slate-500">{{ $repair->device->model ?? 'نامشخص' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <x-enhanced-status-badge :status="$repair->status->value" size="xs" />
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-8 text-center text-slate-400 text-xs">موردی یافت نشد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot name="footer">
            <a href="{{ auth()->user()->isTechnician() ? route('automation.repairs.index') : route('automation.service-orders.index') }}" class="btn-modern btn-modern-primary w-full py-2 text-xs justify-center">مشاهده همه تعمیرات</a>
        </x-slot>
    </x-enhanced-card>
</div>
