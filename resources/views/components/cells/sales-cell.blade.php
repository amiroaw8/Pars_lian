<div class="space-y-6 cell-membrane animate-fade-in" data-cell="sales">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <i class="ti ti-shopping-cart"></i>
            </div>
            بخش فروش و فروشگاه
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('automation.orders.index') }}" class="btn-modern btn-modern-success py-2 px-4 text-xs">
                مدیریت سفارشات
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="stat-card-modern bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-2 -bottom-2 text-amber-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                <i class="ti ti-package text-6xl"></i>
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
            <div class="absolute -right-2 -bottom-2 text-indigo-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                <i class="ti ti-truck text-6xl"></i>
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

    <x-enhanced-card title="آخرین سفارشات فروش" icon="ti-shopping-bag" animated>
        <x-slot name="headerAction">
            <form action="{{ route('automation.dashboard') }}" method="GET" class="relative flex items-center gap-2">
                @if(request('repair_search'))
                    <input type="hidden" name="repair_search" value="{{ request('repair_search') }}">
                @endif
                <input type="text" name="sales_search" value="{{ request('sales_search') }}" 
                       class="form-control-modern py-1.5 px-3 pr-8 text-[10px] w-48" 
                       placeholder="جستجوی سفارشات...">
                <i class="ti ti-search absolute right-2 top-1/2 -translate-y-1/2 text-slate-400"></i>
                @if(request('sales_search'))
                    <a href="{{ route('automation.dashboard', ['repair_search' => request('repair_search')]) }}" class="text-rose-500 hover:text-rose-700">
                        <i class="ti ti-x"></i>
                    </a>
                @endif
            </form>
        </x-slot>
        <div class="overflow-x-auto -mx-6">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-[10px] font-bold">
                        <th class="px-6 py-4">شماره</th>
                        <th class="px-6 py-4">مشتری</th>
                        <th class="px-6 py-4">مبلغ</th>
                        <th class="px-6 py-4">وضعیت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentSales as $order)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('automation.orders.show', $order) }}" class="font-bold text-emerald-600">{{ $order->order_number }}</a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-slate-700">{{ $order->user->name ?? 'نامشخص' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-[10px] font-black text-slate-700">{{ number_format($order->total) }} <span class="text-slate-400 font-normal">تومان</span></div>
                        </td>
                        <td class="px-6 py-4">
                            <x-enhanced-status-badge :status="$order->status->value" size="xs" />
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-8 text-center text-slate-400 text-xs">موردی یافت نشد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot name="footer">
            <a href="{{ route('automation.orders.index') }}" class="btn-modern btn-modern-success w-full py-2 text-xs justify-center">مشاهده همه سفارشات</a>
        </x-slot>
    </x-enhanced-card>
</div>
