@extends('layouts.admin')

@section('title', 'سفارشات سرویس - پارس لیان')

@section('content')
<x-page-header 
    title="سفارشات سرویس" 
    subtitle="مدیریت و پیگیری جامع تمامی سفارشات تعمیرات و خدمات فنی شرکت پارس لیان."
    badge="سیستم مدیریت سفارشات"
    badgeIcon="ti-clipboard-list"
    headerIcon="ti-clipboard-text"
    actionUrl="{{ route('automation.service-orders.create') }}"
    actionText="ثبت سفارش جدید"
    class="mb-8"
/>

<!-- Tab Navigation -->
<div class="mb-6 border-b border-gray-200">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
        <li class="mr-2" role="presentation">
            <a href="{{ route('automation.service-orders.index', ['view' => 'all']) }}" 
               class="inline-block p-4 border-b-2 rounded-t-lg {{ $viewType === 'all' ? 'text-primary-600 border-primary-600 active' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                <i class="ti ti-list mr-2"></i>
                همه سفارشات
                <span class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full ml-1">{{ $tabCounts['all'] ?? $serviceOrders->total() }}</span>
            </a>
        </li>
        @if(auth()->user()->isTechnician())
        <li class="mr-2" role="presentation">
            <a href="{{ route('automation.service-orders.index', ['view' => 'my_repairs']) }}" 
               class="inline-block p-4 border-b-2 rounded-t-lg {{ $viewType === 'my_repairs' ? 'text-amber-600 border-amber-600 active' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                <i class="ti ti-tool mr-2"></i>
                کارتابل فنی (من)
                <span class="bg-amber-100 text-amber-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full ml-1">{{ $myRepairsCount ?? 0 }}</span>
            </a>
        </li>
        <li class="mr-2" role="presentation">
            <a href="{{ route('automation.service-orders.index', ['view' => 'available']) }}" 
               class="inline-block p-4 border-b-2 rounded-t-lg {{ $viewType === 'available' ? 'text-indigo-600 border-indigo-600 active' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                <i class="ti ti-hand-stop mr-2"></i>
                سفارشات آزاد
                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full ml-1">{{ $availableCount ?? ($statusCounts['registered'] ?? 0) }}</span>
            </a>
        </li>
        @endif
        @if(auth()->user()->isAccountant() || auth()->user()->isAdmin())
        <li class="mr-2" role="presentation">
            <a href="{{ route('automation.service-orders.index', ['view' => 'financial']) }}" 
               class="inline-block p-4 border-b-2 rounded-t-lg {{ $viewType === 'financial' ? 'text-emerald-600 border-emerald-600 active' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                <i class="ti ti-cash mr-2"></i>
                امور مالی
                <span class="bg-emerald-100 text-emerald-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full ml-1">{{ $financialCount ?? 0 }}</span>
            </a>
        </li>
        @endif
    </ul>
</div>

<!-- آمار سریع (فقط در تب "همه") -->
@if($viewType === 'all')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 animate-slide-up" style="animation-delay: 100ms;">
    <!-- کل سفارشات -->
    <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-100 group hover:shadow-md transition-all duration-300">
        <div class="absolute top-0 right-0 w-24 h-24 bg-primary-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10 flex flex-col h-full justify-between gap-4">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-xl bg-white border border-primary-100 text-primary-600 flex items-center justify-center text-xl shadow-sm z-10">
                    <i class="ti ti-clipboard-text"></i>
                </div>
                <div class="text-3xl font-black text-slate-800 tracking-tight">{{ $serviceOrders->total() }}</div>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-600 mb-1">کل سفارشات</div>
                <div class="text-[11px] text-slate-400">تعداد کل سفارشات ثبت شده</div>
            </div>
        </div>
    </div>

    <!-- تعیین تکنسین -->
    <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-100 group hover:shadow-md transition-all duration-300">
        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10 flex flex-col h-full justify-between gap-4">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-xl bg-white border border-indigo-100 text-indigo-600 flex items-center justify-center text-xl shadow-sm z-10">
                    <i class="ti ti-user-check"></i>
                </div>
                <div class="text-3xl font-black text-slate-800 tracking-tight">{{ $statusCounts['technician_assigned'] ?? 0 }}</div>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-600 mb-1">تعیین تکنسین</div>
                <div class="text-[11px] text-slate-400">منتظر ارجاع به تکنسین</div>
            </div>
        </div>
    </div>
    
    <!-- در حال تعمیر -->
    <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-100 group hover:shadow-md transition-all duration-300">
        <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10 flex flex-col h-full justify-between gap-4">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-xl bg-white border border-amber-100 text-amber-600 flex items-center justify-center text-xl shadow-sm z-10">
                    <i class="ti ti-tool"></i>
                </div>
                <div class="text-3xl font-black text-slate-800 tracking-tight">{{ $statusCounts['repairing'] ?? 0 }}</div>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-600 mb-1">در حال تعمیر</div>
                <div class="text-[11px] text-slate-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    در حال پردازش فنی
                </div>
            </div>
        </div>
    </div>

    <!-- در انتظار حسابداری -->
    <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-sm border border-slate-100 group hover:shadow-md transition-all duration-300">
        <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10 flex flex-col h-full justify-between gap-4">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-xl bg-white border border-orange-100 text-orange-600 flex items-center justify-center text-xl shadow-sm z-10">
                    <i class="ti ti-calculator"></i>
                </div>
                <div class="text-3xl font-black text-slate-800 tracking-tight">{{ $statusCounts['accounting'] ?? 0 }}</div>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-600 mb-1">در انتظار حسابداری</div>
                <div class="text-[11px] text-slate-400">بررسی هزینه و فاکتور</div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Search & Filter -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-6 animate-slide-up" style="animation-delay: 200ms;">
    <form action="{{ route('automation.service-orders.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
        <input type="hidden" name="view" value="{{ $viewType }}">
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold text-slate-500 mb-2">جستجو</label>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full h-11 pr-11 pl-4 rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500 transition-all text-sm"
                    placeholder="شماره سفارش، نام مشتری، شماره تماس...">
                <div class="absolute right-0 top-0 h-11 w-11 flex items-center justify-center text-slate-400">
                    <i class="ti ti-search text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="w-full md:w-auto flex gap-2">
            <button type="submit" id="filter-btn" class="btn-modern btn-modern-primary h-11 px-6 flex items-center justify-center gap-2 transition-all">
                <i id="filter-icon" class="ti ti-filter"></i>
                <span id="filter-text">اعمال فیلتر</span>
                <span id="filter-loading" class="hidden items-center gap-2">
                    <i class="ti ti-loader animate-spin"></i>
                    <span>در حال پردازش...</span>
                </span>
            </button>
            
            @if(request()->hasAny(['search']))
                <a href="{{ route('automation.service-orders.index', ['view' => $viewType]) }}" class="btn-modern btn-modern-secondary h-11 px-4" title="حذف فیلترها">
                    <i class="ti ti-x"></i>
                </a>
            @endif
        </div>
    </form>
    
    <script>
        document.getElementById('filter-btn').closest('form').addEventListener('submit', function() {
            const btn = document.getElementById('filter-btn');
            const icon = document.getElementById('filter-icon');
            const text = document.getElementById('filter-text');
            const loading = document.getElementById('filter-loading');
            
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            
            icon.classList.add('hidden');
            text.classList.add('hidden');
            loading.classList.remove('hidden');
            loading.classList.add('flex');
        });
    </script>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden animate-slide-up w-full max-w-full" style="animation-delay: 300ms;">
    <div class="overflow-x-auto w-full rounded-xl">
        <table class="min-w-full w-full text-right divide-y divide-gray-200">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase text-slate-500">
                    <th class="px-4 sm:px-6 py-3 sm:py-4 font-bold whitespace-nowrap">شماره سفارش</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 font-bold whitespace-nowrap">مشتری</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 font-bold whitespace-nowrap">دستگاه</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 font-bold whitespace-nowrap">وضعیت</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 font-bold whitespace-nowrap">تکنسین</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 font-bold whitespace-nowrap">تاریخ ثبت</th>
                    <th class="px-4 sm:px-6 py-3 sm:py-4 font-bold whitespace-nowrap text-center">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($serviceOrders as $order)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 font-mono text-sm"><x-hash-ref :value="$order->id" /></span>
                            <span class="text-[10px] text-slate-400 mt-1">{{ $order->service_type === 'on_site' ? 'خدمات در محل' : 'مراجعه حضوری' }}</span>
                        </div>
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center text-sm font-bold shrink-0">
                                {{ substr($order->customer->name ?? '?', 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <span class="text-sm font-bold text-slate-700 block truncate">{{ $order->customer->name ?? 'ناشناس' }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $order->customer->phone ?? '-' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-slate-700">{{ $order->device->model ?? '-' }}</span>
                            <span class="text-[11px] text-slate-400">{{ $order->device->type ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                        <x-enhanced-status-badge :status="$order->status" />
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                        @if($order->technician)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-500 shrink-0">
                                    <i class="ti ti-user"></i>
                                </div>
                                <span class="text-xs text-slate-600">{{ $order->technician->name }}</span>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">تعیین نشده</span>
                        @endif
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-600 dir-ltr text-right">{{ \Morilog\Jalali\Jalalian::fromDateTime($order->created_at)->format('Y/m/d') }}</span>
                            <span class="text-[10px] text-slate-400 dir-ltr text-right">{{ \Morilog\Jalali\Jalalian::fromDateTime($order->created_at)->format('H:i') }}</span>
                        </div>
                    </td>
                    <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                        <div class="flex items-center justify-center gap-2 md:gap-1">
                            <a href="{{ route('automation.service-orders.show', $order) }}" 
                               class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-primary-500 hover:text-primary-600 flex items-center justify-center transition-all shadow-sm hover:shadow-md"
                               title="مشاهده جزئیات">
                                <i class="ti ti-eye"></i>
                            </a>

                            @if(auth()->user()->isTechnician() && !$order->technician_id && $order->status === \App\Enums\ServiceOrderStatus::REGISTERED)
                                <form action="{{ route('automation.service-orders.assign-self', $order) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="btn-modern btn-modern-warning btn-sm">
                                        <i class="ti ti-hand-stop"></i>
                                        تخصیص به من
                                    </button>
                                </form>
                            @elseif(auth()->user()->isTechnician() && $order->technician_id === auth()->id() && $order->status === \App\Enums\ServiceOrderStatus::TECHNICIAN_ASSIGNED)
                                <form action="{{ route('automation.repairs.start', $order) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="btn-modern btn-modern-success btn-sm">
                                        <i class="ti ti-player-play"></i>
                                        شروع تعمیر
                                    </button>
                                </form>
                            @endif
                            
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <form action="{{ route('automation.service-orders.destroy', $order) }}" method="POST" class="inline-block" onsubmit="return confirm('آیا از حذف این سفارش اطمینان دارید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-red-500 hover:text-red-600 flex items-center justify-center transition-all shadow-sm hover:shadow-md"
                                            title="حذف">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <i class="ti ti-search text-2xl text-slate-300"></i>
                            </div>
                            <h3 class="text-slate-900 font-bold mb-1">هیچ سفارشی یافت نشد</h3>
                            <p class="text-slate-500 text-sm">با تغییر فیلترها دوباره تلاش کنید</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
        {{ $serviceOrders->links() }}
    </div>
</div>
@endsection
