<div class="space-y-6 cell-membrane animate-fade-in" data-cell="warehouse">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-slate-800 flex items-center gap-2">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20">
                <i class="ti ti-package"></i>
            </div>
            مدیریت انبار
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('automation.inventory.reports.index') }}" class="btn-modern btn-modern-primary py-2 px-4 text-xs">
                گزارشات
            </a>
            <a href="{{ route('automation.inventory.index') }}" class="btn-modern btn-modern-warning py-2 px-4 text-xs">
                مدیریت موجودی
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4">
        <div class="stat-card-modern bg-white p-5 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-2 -bottom-2 text-amber-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                <i class="ti ti-alert-triangle text-6xl"></i>
            </div>
            <div class="relative z-10">
                <div class="text-2xl font-black text-slate-800 mb-1">{{ $lowStockCount }}</div>
                <div class="text-[11px] font-bold text-slate-500">کالاهای رو به اتمام</div>
            </div>
        </div>
    </div>

    <x-enhanced-card title="کالاهای رو به اتمام" icon="ti-alert-triangle" animated>
        <div class="overflow-x-auto -mx-6">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-slate-500 text-[10px] font-bold">
                        <th class="px-6 py-4">نام کالا</th>
                        <th class="px-6 py-4">موجودی</th>
                        <th class="px-6 py-4">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($lowStockItems as $item)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-slate-700">{{ $item->name }}</div>
                            <div class="text-[10px] text-slate-500">{{ $item->sku }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-600 text-[10px] font-bold">
                                {{ $item->quantity }} {{ $item->unit }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('automation.inventory.edit', $item) }}" class="text-primary-600 hover:text-primary-800">
                                <i class="ti ti-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="py-8 text-center text-slate-400 text-xs">تمامی کالاها موجودی کافی دارند</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot name="footer">
            <a href="{{ route('automation.inventory.index') }}" class="btn-modern btn-modern-warning w-full py-2 text-xs justify-center">مدیریت کامل انبار</a>
        </x-slot>
    </x-enhanced-card>
</div>
