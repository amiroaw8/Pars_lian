@extends('layouts.admin')

@section('title', 'سفارشات فروش - پارس لیان')

@section('content')
<x-page-header 
    title="سفارشات فروش (فروشگاه)" 
    subtitle="مدیریت و پیگیری سفارشات ثبت شده از طریق فروشگاه آنلاین کالا."
    badge="سیستم فروش کالا"
    badgeIcon="ti-shopping-cart"
    headerIcon="ti-receipt-2"
    class="mb-8"
/>

<!-- آمار سریع -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 animate-slide-up" style="animation-delay: 100ms;">
    <div class="stat-card-modern group">
        <div class="stat-icon bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
            <i class="ti ti-shopping-cart text-2xl"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">کل سفارشات</div>
            <div class="stat-value">{{ $orders->total() }}</div>
        </div>
    </div>
    
    <div class="stat-card-modern group">
        <div class="stat-icon bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300">
            <i class="ti ti-clock text-2xl"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">در انتظار بررسی</div>
            <div class="stat-value">{{ $orders->where('status', 'pending')->count() }}</div>
        </div>
    </div>

    <div class="stat-card-modern group">
        <div class="stat-icon bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
            <i class="ti ti-truck text-2xl"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">ارسال شده</div>
            <div class="stat-value">{{ $orders->where('status', 'shipped')->count() }}</div>
        </div>
    </div>

    <div class="stat-card-modern group">
        <div class="stat-icon bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
            <i class="ti ti-currency-dollar text-2xl"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">جمع کل فروش</div>
            <div class="stat-value">{{ number_format($orders->sum('total')) }} <span class="text-xs">تومان</span></div>
        </div>
    </div>
</div>

<!-- جستجو و فیلتر -->
<div class="filter-card animate-slide-up mb-8" style="animation-delay: 200ms;">
    <form method="GET" action="{{ route('automation.orders.index') }}" class="search-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div class="form-group">
                <label for="search" class="form-label">
                    <i class="ti ti-search text-primary-500"></i>
                    جستجو
                </label>
                <div class="relative group">
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="شماره سفارش، نام مشتری، تلفن..."
                           class="form-control pr-10 focus:ring-2 focus:ring-primary-500/20 transition-all">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary-500 transition-colors">
                        <i class="ti ti-keyboard"></i>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">
                    <i class="ti ti-activity text-amber-500"></i>
                    وضعیت سفارش
                </label>
                <select name="status" id="status" class="form-control focus:ring-2 focus:ring-amber-500/20">
                    <option value="">همه وضعیت‌ها</option>
                    @foreach(\App\Enums\OrderStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-modern btn-modern-primary flex-1 justify-center py-2.5 shadow-lg shadow-primary-100">
                    <i class="ti ti-filter"></i>
                    اعمال فیلتر
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('automation.orders.index') }}" class="btn-modern btn-modern-secondary px-3" title="پاک کردن فیلترها">
                        <i class="ti ti-refresh text-lg"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="animate-slide-up" style="animation-delay: 300ms;">
    <x-enhanced-table striped hover responsive title="لیست سفارشات فروش" icon="ti ti-receipt-2">
        <x-slot name="headers">
            <th>شماره سفارش</th>
            <th>مشتری</th>
            <th>مبلغ کل</th>
            <th>وضعیت</th>
            <th>پرداخت</th>
            <th>تاریخ ثبت</th>
            <th class="w-20">عملیات</th>
        </x-slot>

        <x-slot name="rows">
            @forelse($orders as $order)
            <tr class="hover:bg-slate-50/80 transition-all duration-200 group">
                <td>
                    <div class="flex flex-col">
                        <span class="font-black text-primary-600"><x-hash-ref :value="$order->order_number" /></span>
                    </div>
                </td>
                <td>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                            {{ mb_substr($order->shipping_first_name, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</span>
                            <span class="text-[11px] text-slate-500">{{ $order->shipping_phone }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex flex-col">
                        <span class="font-black text-slate-900">{{ number_format($order->total) }}</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase">Toman</span>
                    </div>
                </td>
                <td>
                    <x-enhanced-status-badge :status="$order->status->value" />
                </td>
                <td>
                    @php
                        $pStatus = $order->payment_status;
                        $pColor = match($pStatus->value) {
                            'paid' => 'emerald',
                            'pending' => 'amber',
                            'failed' => 'rose',
                            default => 'slate'
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-{{ $pColor }}-50 text-{{ $pColor }}-600 text-[11px] font-black border border-{{ $pColor }}-100/50">
                        <i class="ti ti-{{ $pStatus->value == 'paid' ? 'check' : ($pStatus->value == 'failed' ? 'x' : 'clock') }}"></i>
                        {{ $pStatus->label() }}
                    </span>
                </td>
                <td>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-slate-700">
                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                {{ \Morilog\Jalali\Jalalian::fromCarbon($order->created_at)->format('Y/m/d') }}
                            @else
                                {{ $order->created_at->format('Y/m/d') }}
                            @endif
                        </span>
                        <span class="text-[10px] text-slate-400">
                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                {{ \Morilog\Jalali\Jalalian::fromCarbon($order->created_at)->format('H:i') }}
                            @else
                                {{ $order->created_at->format('H:i') }}
                            @endif
                        </span>
                    </div>
                </td>
                <td>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('automation.orders.show', $order) }}" 
                           class="w-10 h-10 rounded-xl bg-white text-slate-500 hover:bg-primary-600 hover:text-white flex items-center justify-center transition-all duration-300 shadow-sm border border-slate-100" 
                           title="مشاهده جزئیات">
                            <i class="ti ti-eye text-lg"></i>
                        </a>
                        <form action="{{ route('automation.orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('آیا از حذف این سفارش مطمئن هستید؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-10 h-10 rounded-xl bg-white text-slate-500 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all duration-300 shadow-sm border border-slate-100" 
                                    title="حذف سفارش">
                                <i class="ti ti-trash text-lg"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-12 text-center">
                    <div class="flex flex-col items-center justify-center text-slate-400">
                        <i class="ti ti-package-off text-6xl mb-4 opacity-20"></i>
                        <p class="font-bold">هیچ سفارشی یافت نشد</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </x-slot>
    </x-enhanced-table>
    
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
</div>
@endsection
