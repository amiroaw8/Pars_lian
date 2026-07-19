<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8 animate-slide-up" style="animation-delay: 0.2s;">
    <!-- توزیع نقش‌ها -->
    <x-enhanced-card title="توزیع نقش‌های کاربری" icon="ti-chart-pie">
        <div class="space-y-6">
            @php
                $roleColors = [
                    'super_admin' => 'bg-rose-500',
                    'admin' => 'bg-amber-500',
                    'technician' => 'bg-emerald-500',
                    'receptionist' => 'bg-primary-500',
                    'warehouse' => 'bg-purple-500',
                    'accountant' => 'bg-sky-500'
                ];
                $roleNames = [
                    'super_admin' => 'سوپر ادمین',
                    'admin' => 'مدیر سیستم',
                    'technician' => 'تعمیرکار',
                    'receptionist' => 'پذیرش',
                    'warehouse' => 'انباردار',
                    'accountant' => 'حسابدار'
                ];
                $userRoles = $userRoles ?? collect();
                $totalRoles = $userRoles->sum();
            @endphp

            @if($userRoles->isNotEmpty())
                <div class="flex h-4 w-full rounded-full overflow-hidden bg-slate-100 mb-8">
                    @foreach($userRoles as $role => $count)
                        @php $percent = ($count / $totalRoles) * 100; @endphp
                        <div class="{{ $roleColors[$role] ?? 'bg-slate-400' }} h-full" style="width: <?php echo $percent; ?>%;" title="{{ $roleNames[$role] ?? $role }}: {{ $count }}"></div>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @foreach($userRoles as $role => $count)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $roleColors[$role] ?? 'bg-slate-400' }}"></span>
                                <span class="text-[11px] font-bold text-slate-700">{{ $roleNames[$role] ?? $role }}</span>
                            </div>
                            <span class="text-[11px] font-black text-slate-500">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                    <i class="ti ti-users text-4xl mb-3"></i>
                    <p class="text-sm">اطلاعاتی یافت نشد</p>
                </div>
            @endif
        </div>
    </x-enhanced-card>

    <!-- وضعیت سفارشات -->
    <x-enhanced-card title="وضعیت فعلی سفارشات" icon="ti-adjustments-horizontal">
        <div class="space-y-6">
            @php
                $statusStats = [
                    'pending' => ['count' => $stats['pending_orders'] ?? 0, 'label' => 'در انتظار', 'color' => 'bg-amber-500'],
                    'completed' => ['count' => $stats['completed_orders'] ?? 0, 'label' => 'تکمیل شده', 'color' => 'bg-emerald-500'],
                    'other' => ['count' => ($stats['total_orders'] ?? 0) - (($stats['pending_orders'] ?? 0) + ($stats['completed_orders'] ?? 0)), 'label' => 'سایر موارد', 'color' => 'bg-slate-400']
                ];
                $totalOrders = $stats['total_orders'] ?? 1;
            @endphp

            <div class="relative pt-1">
                @foreach($statusStats as $status => $data)
                    @php $percent = ($data['count'] / max(1, $totalOrders)) * 100; @endphp
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] font-bold text-slate-600">{{ $data['label'] }}</span>
                            <span class="text-[11px] font-black text-slate-800">{{ round($percent) }}%</span>
                        </div>
                        <div class="overflow-hidden h-2 text-xs flex rounded-full bg-slate-100">
                            <div style="width: <?php echo $percent; ?>%;" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $data['color'] }} transition-all duration-1000"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-enhanced-card>

    <!-- آمار ماهانه -->
    <x-enhanced-card title="آمار ماهانه سفارشات" icon="ti-calendar-stats">
        <div class="flex items-end justify-between gap-1.5 h-48 px-2 mt-4">
            @php
                $monthlyStats = $monthlyStats ?? collect();
                $monthlyStatsArray = is_array($monthlyStats) ? $monthlyStats : $monthlyStats->toArray();
                $maxVal = !empty($monthlyStatsArray) ? max($monthlyStatsArray) : 10;
                $months = [
                    1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر',
                    5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان',
                    9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
                ];
            @endphp

            @for($month = 1; $month <= 12; $month++)
                @php
                    $val = $monthlyStatsArray[$month] ?? 0;
                    $height = $maxVal > 0 ? ($val / $maxVal) * 100 : 0;
                    $isCurrentMonth = \Morilog\Jalali\Jalalian::now()->getMonth() == $month;
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1.5 group">
                    <div class="relative w-full flex justify-center items-end h-32">
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[9px] py-0.5 px-1.5 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                            {{ $val }}
                        </div>
                        <div class="w-full max-w-[8px] rounded-t-full transition-all duration-500 {{ $isCurrentMonth ? 'bg-primary-500 shadow-lg shadow-primary-200' : 'bg-slate-200 group-hover:bg-primary-300' }}" style="height: <?php echo max(5, $height); ?>%;"></div>
                    </div>
                    <div class="text-[8px] font-bold {{ $isCurrentMonth ? 'text-primary-600' : 'text-slate-400' }} vertical-text whitespace-nowrap">
                        {{ $months[$month] }}
                    </div>
                </div>
            @endfor
        </div>
    </x-enhanced-card>
</div>

<!-- ردیف دوم تحلیل BI -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 animate-slide-up" style="animation-delay: 0.25s;">
    <!-- عملکرد تکنسین‌ها -->
    <x-enhanced-card title="عملکرد تکنسین‌ها (BI)" icon="ti-user-bolt">
        <div class="space-y-4">
            @forelse($advancedStats['technician_performance'] ?? [] as $perf)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 hover:border-primary-200 transition-colors">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-[10px]">
                                {{ mb_substr($perf['name'], 0, 1) }}
                            </div>
                            <span class="text-xs font-bold text-slate-700">{{ $perf['name'] }}</span>
                        </div>
                        <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">
                            {{ $perf['completed_count'] }} تکمیل شده
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                            @php $perfPercent = ($perf['completed_count'] / max(1, array_sum(array_column($advancedStats['technician_performance'], 'completed_count')))) * 100; @endphp
                            <div class="h-full bg-primary-500 rounded-full" style="width: <?php echo $perfPercent; ?>%;"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500">{{ number_format($perf['total_revenue']) }} ریال</span>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-xs">داده‌ای برای نمایش وجود ندارد</div>
            @endforelse
        </div>
    </x-enhanced-card>

    <!-- توزیع دستگاه‌ها -->
    <x-enhanced-card title="توزیع انواع دستگاه‌های تعمیری" icon="ti-devices">
        <div class="space-y-4">
            @forelse($advancedStats['device_type_distribution'] ?? [] as $device)
                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-600">
                            <i class="ti ti-device-laptop text-lg"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800">{{ $device['device_type'] ?: 'سایر' }}</div>
                            <div class="text-[10px] text-slate-500">{{ $device['count'] }} مورد ثبت شده</div>
                        </div>
                    </div>
                    @php $devicePercent = ($device['count'] / max(1, array_sum(array_column($advancedStats['device_type_distribution'], 'count')))) * 100; @endphp
                    <div class="text-xs font-black text-slate-700">{{ round($devicePercent) }}%</div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-xs">داده‌ای برای نمایش وجود ندارد</div>
            @endforelse
        </div>
    </x-enhanced-card>
</div>

<!-- محصولات پرفروش و آمار مشتریان -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8 animate-slide-up" style="animation-delay: 0.3s;">
    <!-- محصولات پرفروش -->
    <x-enhanced-card title="محصولات پرفروش" icon="ti-shopping-cart-bolt" class="lg:col-span-2">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($advancedStats['top_selling_products'] ?? [] as $product)
                <div class="flex items-center gap-4 p-3 rounded-xl bg-slate-50/50 border border-slate-100">
                    <div class="w-12 h-12 rounded-lg bg-white shadow-sm flex items-center justify-center text-primary-600">
                        <i class="ti ti-package text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-slate-800 truncate">{{ $product['product_name'] }}</div>
                        <div class="text-[10px] text-slate-500">فروش: {{ $product['total_quantity'] }} عدد</div>
                    </div>
                    <div class="text-xs font-black text-emerald-600">
                        {{ number_format($product['total_revenue']) }} <span class="text-[9px]">ریال</span>
                    </div>
                </div>
            @empty
                <div class="col-span-2 py-8 text-center text-slate-400 text-xs">داده‌ای برای نمایش وجود ندارد</div>
            @endforelse
        </div>
    </x-enhanced-card>

    <!-- آمار مشتریان -->
    <x-enhanced-card title="وضعیت مشتریان" icon="ti-users-group">
        <div class="space-y-6">
            <div class="p-4 rounded-2xl bg-primary-50 border border-primary-100">
                <div class="text-[10px] font-bold text-primary-600 mb-1">مشتریان وفادار</div>
                <div class="text-2xl font-black text-primary-800">{{ $customerStats['loyal'] ?? 0 }}</div>
                <div class="text-[9px] text-primary-500 mt-1">بیش از ۳ سفارش ثبت شده</div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-500 mb-1">مشتریان جدید</div>
                    <div class="text-lg font-black text-slate-800">{{ $customerStats['new_this_month'] ?? 0 }}</div>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-500 mb-1">کل مشتریان</div>
                    <div class="text-lg font-black text-slate-800">{{ $customerStats['total'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </x-enhanced-card>
</div>

<!-- فعالیت‌های اخیر سیستم -->
<x-enhanced-card animated>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <i class="ti ti-activity text-primary-600 text-xl"></i>
            <h3 class="card-title text-slate-800 font-bold">آخرین فعالیت‌های کل سیستم</h3>
        </div>
    </x-slot>
    <x-enhanced-table>
        <x-slot name="headers">
            <th class="px-6 py-4">کاربر</th>
            <th class="px-6 py-4">نوع عملیات</th>
            <th class="px-6 py-4">شرح فعالیت</th>
            <th class="px-6 py-4">سفارش</th>
            <th class="px-6 py-4">زمان</th>
        </x-slot>
        <x-slot name="rows">
            @php
                try {
                    $recentActivities = \App\Models\OrderLog::with(['serviceOrder', 'user'])
                                           ->latest()
                                           ->take(15)
                                           ->get();
                } catch (\Exception $e) {
                    $recentActivities = collect();
                }
            @endphp
            @forelse($recentActivities as $activity)
        <tr class="group hover:bg-slate-50/50 transition-colors">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs border-2 border-white shadow-sm group-hover:scale-110 transition-transform">
                        @php
                            $initials = collect(explode(' ', $activity->user->name ?? 'س'))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('');
                        @endphp
                        {{ $initials ?: 'U' }}
                    </div>
                    <div class="font-bold text-slate-700 text-xs">{{ $activity->user->name ?? 'نامشخص' }}</div>
                </div>
            </td>
            <td class="px-6 py-4">
                @switch($activity->action)
                    @case('created')
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider">
                            <i class="ti ti-plus text-xs"></i>
                            ثبت جدید
                        </span>
                        @break
                    @case('updated')
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wider">
                            <i class="ti ti-edit text-xs"></i>
                            ویرایش
                        </span>
                        @break
                    @case('status_changed')
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-black bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-wider">
                            <i class="ti ti-refresh text-xs"></i>
                            تغییر وضعیت
                        </span>
                        @break
                    @case('attachment_added')
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-black bg-purple-50 text-purple-600 border border-purple-100 uppercase tracking-wider">
                            <i class="ti ti-paperclip text-xs"></i>
                            فایل ضمیمه
                        </span>
                        @break
                    @default
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-[10px] font-black bg-slate-50 text-slate-600 border border-slate-100 uppercase tracking-wider">
                            <i class="ti ti-dots text-xs"></i>
                            سایر
                        </span>
                @endswitch
            </td>
            <td class="px-6 py-4">
                <div class="text-xs text-slate-600 font-medium leading-relaxed max-w-xs truncate" title="{{ $activity->description }}">
                    {{ $activity->description }}
                </div>
            </td>
            <td class="px-6 py-4">
                <a href="{{ route('automation.service-orders.show', $activity->service_order_id) }}"
                   class="inline-flex items-center gap-1 text-blue-600 font-black text-xs hover:text-blue-700 transition-colors bg-blue-50 px-2 py-1 rounded-md">
                    <i class="ti ti-hash text-[10px]"></i>
                    {{ $activity->service_order_id }}
                </a>
            </td>
            <td class="px-6 py-4">
                <div class="text-[10px] text-slate-400 font-bold flex items-center gap-1.5">
                    <i class="ti ti-clock-hour-4"></i>
                    {{ $activity->created_at->diffForHumans() }}
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <i class="ti ti-ghost text-3xl opacity-20"></i>
                    </div>
                    <p class="font-bold text-sm">هیچ فعالیتی ثبت نشده است</p>
                </div>
            </td>
        </tr>
        @endforelse
        </x-slot>
    </x-enhanced-table>
</x-enhanced-card>

<style>
    .vertical-text {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
    }
</style>
@endsection
