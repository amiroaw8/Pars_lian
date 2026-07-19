@extends('layouts.admin')

@section('title', 'مدیریت فروش حضوری - پارس لیان')

@section('content')
<x-page-header
    title="مدیریت فروش حضوری"
    subtitle="لیست و پیگیری فروش‌های ثبت‌شده از پنل POS"
    badge="فروش حضوری"
    badgeIcon="ti-cash-register"
    headerIcon="ti-report-money"
    actionUrl="{{ route('automation.pos.index') }}"
    actionText="ثبت فروش جدید"
    actionIcon="ti-plus"
    class="mb-8"
/>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8 animate-slide-up">
    <div class="stat-card-modern group">
        <div class="stat-icon bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
            <i class="ti ti-receipt-2 text-2xl"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">کل فروش‌های حضوری</div>
            <div class="stat-value">{{ number_format($stats['total_count']) }}</div>
        </div>
    </div>

    <div class="stat-card-modern group">
        <div class="stat-icon bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
            <i class="ti ti-currency-dollar text-2xl"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">جمع کل فروش</div>
            <div class="stat-value">{{ number_format($stats['total_amount']) }} <span class="text-xs">تومان</span></div>
        </div>
    </div>

    <div class="stat-card-modern group">
        <div class="stat-icon bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
            <i class="ti ti-calendar-event text-2xl"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">فروش امروز</div>
            <div class="stat-value">{{ number_format($stats['today_count']) }} <span class="text-xs text-slate-400">({{ number_format($stats['today_amount']) }} تومان)</span></div>
        </div>
    </div>

    <div class="stat-card-modern group">
        <div class="stat-icon bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300">
            <i class="ti ti-alert-circle text-2xl"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">فروش‌های بدهی</div>
            <div class="stat-value">{{ number_format($stats['debt_count']) }}</div>
        </div>
    </div>
</div>

<div class="filter-card animate-slide-up mb-8">
    <form method="GET" action="{{ route('automation.pos.sales') }}" class="search-form">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 items-end">
            <div class="form-group xl:col-span-2">
                <label for="search" class="form-label"><i class="ti ti-search text-primary-500"></i> جستجو</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       placeholder="شماره فاکتور، نام یا تلفن مشتری..."
                       class="form-control">
            </div>

            <div class="form-group">
                <label for="payment_method" class="form-label">روش پرداخت</label>
                <select name="payment_method" id="payment_method" class="form-control w-full">
                    <option value="">همه</option>
                    <option value="cod" @selected(request('payment_method') === 'cod')>نقدی</option>
                    <option value="card" @selected(request('payment_method') === 'card')>کارت</option>
                    <option value="debt" @selected(request('payment_method') === 'debt')>بدهی</option>
                </select>
            </div>

            <div class="form-group">
                <label for="payment_status" class="form-label">وضعیت پرداخت</label>
                <select name="payment_status" id="payment_status" class="form-control w-full">
                    <option value="">همه</option>
                    @foreach(\App\Enums\PaymentStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('payment_status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-modern btn-modern-primary flex-1 justify-center">
                    <i class="ti ti-filter"></i>
                    فیلتر
                </button>
                @if(request()->hasAny(['search', 'payment_method', 'payment_status', 'from_date', 'to_date']))
                    <a href="{{ route('automation.pos.sales') }}" class="btn-modern btn-modern-secondary px-3" title="پاک کردن فیلترها">
                        <i class="ti ti-refresh text-lg"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="animate-slide-up">
    <x-enhanced-table striped hover responsive title="لیست فروش‌های حضوری" icon="ti ti-cash-register">
        <x-slot name="headers">
            <th>شماره فاکتور</th>
            <th>مشتری</th>
            <th>اقلام</th>
            <th>مبلغ</th>
            <th>پرداخت</th>
            <th>تاریخ</th>
            <th class="w-20">عملیات</th>
        </x-slot>

        <x-slot name="rows">
            @forelse($orders as $order)
            <tr class="hover:bg-slate-50/80 transition-all duration-200 group">
                <td>
                    <span class="font-black text-primary-600"><x-hash-ref :value="$order->order_number" /></span>
                </td>
                <td>
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-800">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</span>
                        <span class="text-[11px] text-slate-500">{{ $order->shipping_phone }}</span>
                    </div>
                </td>
                <td>
                    <span class="text-sm font-bold text-slate-600">{{ $order->items->count() }} قلم</span>
                </td>
                <td>
                    <span class="font-black text-slate-900">{{ number_format($order->total) }}</span>
                    <span class="text-[10px] text-slate-400 block">تومان</span>
                </td>
                <td>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-bold text-slate-600">{{ $order->payment_method_label }}</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-black px-2 py-1 rounded-lg w-fit {{ $order->payment_status->value === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                            {{ $order->payment_status->label() }}
                        </span>
                    </div>
                </td>
                <td>
                    <span class="text-sm font-bold text-slate-700">
                        {{ jalali_date($order->created_at, 'Y/m/d H:i') }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('automation.orders.show', $order) }}"
                       class="w-10 h-10 rounded-xl bg-white text-slate-500 hover:bg-primary-600 hover:text-white flex items-center justify-center transition-all duration-300 shadow-sm border border-slate-100"
                       title="مشاهده جزئیات">
                        <i class="ti ti-eye text-lg"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-12 text-center">
                    <div class="flex flex-col items-center justify-center text-slate-400">
                        <i class="ti ti-cash-register-off text-6xl mb-4 opacity-20"></i>
                        <p class="font-bold mb-4">هنوز فروش حضوری ثبت نشده است</p>
                        <a href="{{ route('automation.pos.index') }}" class="btn-modern btn-modern-primary">
                            <i class="ti ti-plus"></i>
                            ثبت اولین فروش
                        </a>
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
