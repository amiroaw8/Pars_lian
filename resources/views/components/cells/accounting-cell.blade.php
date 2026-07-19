<div class="space-y-6 cell-membrane animate-fade-in" data-cell="accounting">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
            <div class="w-10 h-10 rounded-xl bg-purple-500 text-white flex items-center justify-center shadow-lg shadow-purple-500/20">
                <i class="ti ti-report-money"></i>
            </div>
            بخش حسابداری
        </h2>
        <a href="{{ route('automation.accounting.index') }}" class="btn-modern btn-modern-neutral py-2 px-4 text-xs">
            پنل حسابداری
        </a>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="stat-card-modern bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-2 -bottom-2 text-emerald-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                <i class="ti ti-cash text-6xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-2xl font-black text-emerald-600 mb-1">{{ number_format($stats['today_sales']) }}</div>
                <div class="text-[11px] font-bold text-slate-500">فروش امروز</div>
            </div>
        </div>
        <div class="stat-card-modern bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-2 -bottom-2 text-indigo-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                <i class="ti ti-chart-line text-6xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-2xl font-black text-indigo-600 mb-1">{{ number_format($stats['monthly_income']) }}</div>
                <div class="text-[11px] font-bold text-slate-500">درآمد ماه</div>
            </div>
        </div>
        <div class="stat-card-modern bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-2 -bottom-2 text-blue-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                <i class="ti ti-tool text-6xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-2xl font-black text-blue-600 mb-1">{{ $stats['total_repairs'] }}</div>
                <div class="text-[11px] font-bold text-slate-500">کل پرونده‌های تعمیرات</div>
            </div>
        </div>
        <div class="stat-card-modern bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-2 -bottom-2 text-purple-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                <i class="ti ti-shopping-cart text-6xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-2xl font-black text-purple-600 mb-1">{{ $stats['total_orders'] }}</div>
                <div class="text-[11px] font-bold text-slate-500">کل سفارشات فروشگاه</div>
            </div>
        </div>
    </div>

    <x-enhanced-card title="آخرین تراکنش‌ها" icon="ti-list-details" animated>
        <div class="overflow-x-auto -mx-6">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-[10px] font-bold">
                        <th class="px-6 py-4">شرح</th>
                        <th class="px-6 py-4">مبلغ</th>
                        <th class="px-6 py-4">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentSales as $sale)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-slate-700">{{ $sale->description ?: 'ثبت فروش' }}</div>
                        </td>
                        <td class="px-6 py-4 text-emerald-600 font-bold text-xs">
                            {{ number_format($sale->amount) }}
                        </td>
                        <td class="px-6 py-4 text-[10px] text-slate-500">
                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                {{ \Morilog\Jalali\Jalalian::fromDateTime($sale->created_at)->format('Y/m/d') }}
                            @else
                                {{ $sale->created_at->format('Y/m/d') }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="py-8 text-center text-slate-400 text-xs">تراکنشی یافت نشد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-enhanced-card>

    <x-enhanced-card title="دسترسی سریع" icon="ti-rocket" animated>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('automation.accounting.create-sale') }}" class="quick-action-tile quick-action-tile--primary group">
                <i class="ti ti-plus text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-[10px] font-bold">ثبت فروش جدید</span>
            </a>
            <a href="{{ route('automation.accounting.index') }}" class="quick-action-tile quick-action-tile--indigo group">
                <i class="ti ti-report text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <span class="text-[10px] font-bold">گزارشات مالی</span>
            </a>
        </div>
    </x-enhanced-card>
</div>
