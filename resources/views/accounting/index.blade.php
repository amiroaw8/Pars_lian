@extends('layouts.admin')

@section('title', 'حسابداری - پارس لیان')

@section('content')
<div class="relative pb-12">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-[30rem] h-[30rem] bg-blue-500/10 rounded-full blur-[100px] animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-[25rem] h-[25rem] bg-indigo-500/10 rounded-full blur-[100px] animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-[20rem] h-[20rem] bg-emerald-500/5 rounded-full blur-[100px] animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-16 relative z-10">
        <!-- Modern Header -->
        <x-page-header 
            title="سیستم حسابداری" 
            subtitle="مدیریت یکپارچه جریان‌های مالی، پیگیری تراکنش‌های فروش کالا و خدمات تعمیرات در یک نگاه هوشمند."
            badge="Financial Overview"
            badgeIcon="ti-calculator"
            headerIcon="ti-calculator"
            actionUrl="{{ route('automation.accounting.create-sale') }}"
            actionText="ثبت فروش جدید"
            class="mb-16"
        >
            <x-slot name="extraActions">
                <a href="{{ route('automation.accounting.proforma.create') }}" class="btn-modern btn-modern-secondary flex items-center gap-2">
                    <i class="ti ti-file-invoice"></i>
                    <span>پیش‌فاکتور</span>
                </a>
                <a href="{{ route('automation.accounting.expenses.index') }}" class="btn-modern btn-modern-secondary flex items-center gap-2">
                    <i class="ti ti-cash"></i>
                    <span>مدیریت هزینه‌ها</span>
                </a>
            </x-slot>
        </x-page-header>

        <!-- Customer Financial Insights -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-6">
                <h3 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                    <i class="ti ti-alert-triangle text-rose-500"></i>
                    مشتریان بدهکار
                </h3>
                <div class="space-y-3">
                    @forelse($debtorCustomers as $row)
                    <div class="flex justify-between items-center p-3 bg-rose-50/50 rounded-xl">
                        <a href="{{ route('automation.customers.show', $row['customer']) }}" class="font-bold text-slate-800 hover:text-blue-600">{{ $row['customer']->name }}</a>
                        <span class="text-sm font-black text-rose-600">{{ number_format($row['total_debt']) }} تومان</span>
                    </div>
                    @empty
                    <p class="text-slate-400 text-sm font-bold">بدهکاری ثبت نشده</p>
                    @endforelse
                </div>
            </div>
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-6">
                <h3 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                    <i class="ti ti-circle-check text-emerald-500"></i>
                    مشتریان خوش‌حساب
                </h3>
                <div class="space-y-3">
                    @forelse($goodPayerCustomers as $row)
                    <div class="flex justify-between items-center p-3 bg-emerald-50/50 rounded-xl">
                        <a href="{{ route('automation.customers.show', $row['customer']) }}" class="font-bold text-slate-800 hover:text-blue-600">{{ $row['customer']->name }}</a>
                        <span class="text-sm font-black text-emerald-600">{{ number_format($row['total_paid']) }} تومان</span>
                    </div>
                    @empty
                    <p class="text-slate-400 text-sm font-bold">موردی یافت نشد</p>
                    @endforelse
                </div>
            </div>
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-6">
                <h3 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                    <i class="ti ti-diamond text-indigo-500"></i>
                    مشتریان باارزش
                </h3>
                <div class="space-y-3">
                    @forelse($valuableCustomers as $row)
                    <div class="flex justify-between items-center p-3 bg-indigo-50/50 rounded-xl">
                        <a href="{{ route('automation.customers.show', $row['customer']) }}" class="font-bold text-slate-800 hover:text-blue-600">{{ $row['customer']->name }}</a>
                        <span class="text-sm font-black text-indigo-600">{{ number_format($row['lifetime_value']) }} تومان</span>
                    </div>
                    @empty
                    <p class="text-slate-400 text-sm font-bold">موردی یافت نشد</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Financial Stats Grid - Redesigned -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Shop Sales -->
            <div class="group relative overflow-hidden bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-xl shadow-slate-200/40 animate-slide-up hover:-translate-y-2 transition-all duration-500">
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <i class="ti ti-shopping-cart"></i>
                        </div>
                        <span class="text-[10px] font-black text-blue-400 bg-blue-50/50 px-2 py-1 rounded-full">Shop</span>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">فروش فروشگاه</div>
                        <div class="text-2xl font-black text-slate-900 flex items-baseline gap-1">
                            {{ number_format($totalShopSales) }}
                            <span class="text-[10px] font-bold text-slate-400">تومان</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manual Sales -->
            <div class="group relative overflow-hidden bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-xl shadow-slate-200/40 animate-slide-up hover:-translate-y-2 transition-all duration-500" style="animation-delay: 0.1s">
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <i class="ti ti-receipt-2"></i>
                        </div>
                        <span class="text-[10px] font-black text-indigo-400 bg-indigo-50/50 px-2 py-1 rounded-full">Direct</span>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">فروش حضوری</div>
                        <div class="text-2xl font-black text-slate-900 flex items-baseline gap-1">
                            {{ number_format($totalInPersonSales ?? (($totalManualSales ?? 0) + ($totalPosSales ?? 0))) }}
                            <span class="text-[10px] font-bold text-slate-400">تومان</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Services -->
            <div class="group relative overflow-hidden bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-xl shadow-slate-200/40 animate-slide-up hover:-translate-y-2 transition-all duration-500" style="animation-delay: 0.2s">
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <i class="ti ti-tool"></i>
                        </div>
                        <span class="text-[10px] font-black text-emerald-400 bg-emerald-50/50 px-2 py-1 rounded-full">Repairs</span>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">خدمات تعمیرات</div>
                        <div class="text-2xl font-black text-slate-900 flex items-baseline gap-1">
                            {{ number_format($totalServices) }}
                            <span class="text-[10px] font-bold text-slate-400">تومان</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Income -->
            <div class="group relative overflow-hidden bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-xl shadow-slate-200/40 animate-slide-up hover:-translate-y-2 transition-all duration-500" style="animation-delay: 0.3s">
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <i class="ti ti-wallet"></i>
                        </div>
                        <span class="text-[10px] font-black text-blue-400 bg-blue-50/50 px-2 py-1 rounded-full">Revenue</span>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">جمع کل درآمد</div>
                        <div class="text-2xl font-black text-slate-900 flex items-baseline gap-1">
                            {{ number_format($totalIncome) }}
                            <span class="text-[10px] font-bold text-slate-400">تومان</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Expenses -->
            <div class="group relative overflow-hidden bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-xl shadow-slate-200/40 animate-slide-up hover:-translate-y-2 transition-all duration-500" style="animation-delay: 0.4s">
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <i class="ti ti-cash-off"></i>
                        </div>
                        <span class="text-[10px] font-black text-rose-400 bg-rose-50/50 px-2 py-1 rounded-full">Outgoings</span>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">جمع هزینه‌ها</div>
                        <div class="text-2xl font-black text-slate-900 flex items-baseline gap-1">
                            {{ number_format($totalExpenses) }}
                            <span class="text-[10px] font-bold text-slate-400">تومان</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="group relative overflow-hidden bg-slate-900 rounded-[2.5rem] p-8 shadow-xl shadow-slate-900/40 animate-slide-up hover:-translate-y-2 transition-all duration-500 lg:col-span-2" style="animation-delay: 0.5s">
                <div class="relative z-10 flex items-center justify-between h-full">
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/10 text-white rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform backdrop-blur-md">
                                <i class="ti ti-chart-pie"></i>
                            </div>
                            <span class="text-[10px] font-black text-emerald-400 bg-white/5 px-3 py-1.5 rounded-full border border-white/5">Net Profit</span>
                        </div>
                        <div>
                            <div class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">سود خالص عملیاتی</div>
                            <div class="text-4xl font-black text-white flex items-baseline gap-2">
                                {{ number_format($netProfit) }}
                                <span class="text-sm font-bold text-slate-500">تومان</span>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="text-right space-y-1">
                            <div class="text-[10px] font-black text-slate-500 uppercase">Margin Status</div>
                            <div class="text-sm font-bold text-emerald-500 flex items-center gap-1 justify-end">
                                <i class="ti ti-trending-up"></i>
                                {{ $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 1) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Breakdown Details -->
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden animate-slide-up">
            <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-black text-slate-800 flex items-center gap-3">
                        <i class="ti ti-list-details text-indigo-500"></i>
                        شرح جزئیات محاسبات
                    </h2>
                    <p class="text-xs text-slate-500 font-bold mt-1">
                        @if($startDate || $endDate)
                            بازه گزارش:
                            {{ $startDate ?: 'ابتدا' }}
                            تا
                            {{ $endDate ?: 'امروز' }}
                        @else
                            همه دوره‌ها (بدون فیلتر تاریخ)
                        @endif
                    </p>
                </div>
                <div class="text-left">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">فرمول درآمد</div>
                    <div class="text-xs font-bold text-slate-600 dir-ltr text-right">
                        فروش آنلاین + فروش حضوری + خدمات تعمیر
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto w-full rounded-xl border border-gray-200">
                <table class="min-w-full w-full text-right">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="px-4 sm:px-8 py-3 sm:py-4 whitespace-nowrap text-[10px] font-black text-slate-400 uppercase tracking-wider">عنوان</th>
                            <th class="px-4 sm:px-8 py-3 sm:py-4 whitespace-nowrap text-[10px] font-black text-slate-400 uppercase tracking-wider">منبع داده</th>
                            <th class="px-4 sm:px-8 py-3 sm:py-4 whitespace-nowrap text-center text-[10px] font-black text-slate-400 uppercase tracking-wider">تعداد</th>
                            <th class="px-4 sm:px-8 py-3 sm:py-4 whitespace-nowrap text-left text-[10px] font-black text-slate-400 uppercase tracking-wider">مبلغ (تومان)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($financialBreakdown ?? [] as $row)
                            @if($row['key'] !== 'expenses')
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 sm:px-8 py-4 sm:py-5 whitespace-nowrap">
                                    <span class="font-black text-slate-800 text-sm">{{ $row['label'] }}</span>
                                </td>
                                <td class="px-4 sm:px-8 py-4 sm:py-5">
                                    <p class="text-xs text-slate-500 font-medium leading-relaxed max-w-xl">{{ $row['source'] }}</p>
                                </td>
                                <td class="px-4 sm:px-8 py-4 sm:py-5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-black">
                                        {{ number_format($row['count']) }}
                                        <span class="text-slate-400 font-bold">{{ $row['count_label'] }}</span>
                                    </span>
                                </td>
                                <td class="px-4 sm:px-8 py-4 sm:py-5 text-left whitespace-nowrap">
                                    <span class="font-black text-slate-900">{{ number_format($row['amount']) }}</span>
                                </td>
                            </tr>
                            @endif
                        @endforeach

                        <tr class="bg-blue-50/40">
                            <td class="px-4 sm:px-8 py-4 sm:py-5 font-black text-blue-800 whitespace-nowrap" colspan="2">
                                <span class="text-sm">جمع درآمد (فروش + خدمات)</span>
                                <p class="text-[11px] text-blue-600/80 font-bold mt-1">
                                    {{ number_format($totalShopSales ?? 0) }}
                                    + {{ number_format($totalInPersonSales ?? (($totalPosSales ?? 0) + ($totalManualSales ?? 0))) }}
                                    + {{ number_format($totalServices ?? 0) }}
                                    = {{ number_format($totalIncome ?? 0) }}
                                </p>
                            </td>
                            <td class="px-4 sm:px-8 py-4 sm:py-5 text-center text-xs font-bold text-blue-600 whitespace-nowrap">—</td>
                            <td class="px-4 sm:px-8 py-4 sm:py-5 text-left whitespace-nowrap">
                                <span class="text-lg font-black text-blue-700">{{ number_format($totalIncome ?? 0) }}</span>
                            </td>
                        </tr>

                        @php $expenseRow = collect($financialBreakdown ?? [])->firstWhere('key', 'expenses'); @endphp
                        @if($expenseRow)
                        <tr class="hover:bg-rose-50/30 transition-colors">
                            <td class="px-4 sm:px-8 py-4 sm:py-5 whitespace-nowrap">
                                <span class="font-black text-rose-700 text-sm">{{ $expenseRow['label'] }}</span>
                            </td>
                            <td class="px-4 sm:px-8 py-4 sm:py-5">
                                <p class="text-xs text-slate-500 font-medium leading-relaxed max-w-xl">{{ $expenseRow['source'] }}</p>
                            </td>
                            <td class="px-4 sm:px-8 py-4 sm:py-5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-rose-50 text-rose-600 text-[11px] font-black">
                                    {{ number_format($expenseRow['count']) }}
                                    <span class="text-rose-400 font-bold">{{ $expenseRow['count_label'] }}</span>
                                </span>
                            </td>
                            <td class="px-4 sm:px-8 py-4 sm:py-5 text-left whitespace-nowrap">
                                <bdi dir="ltr" class="font-black text-rose-600">−{{ number_format($expenseRow['amount']) }}</bdi>
                            </td>
                        </tr>
                        @endif

                        <tr class="bg-slate-900 text-white">
                            <td class="px-4 sm:px-8 py-5 sm:py-6 font-black whitespace-nowrap" colspan="2">
                                <span class="text-sm">سود خالص عملیاتی (تحقق‌یافته)</span>
                                <p class="text-[11px] text-slate-400 font-bold mt-1">
                                    جمع درآمد {{ number_format($totalIncome ?? 0) }} منهای هزینه‌ها {{ number_format($totalExpenses ?? 0) }}
                                    @if(($totalIncome ?? 0) > 0)
                                        — حاشیه سود {{ round((($netProfit ?? 0) / $totalIncome) * 100, 1) }}٪
                                    @endif
                                </p>
                            </td>
                            <td class="px-4 sm:px-8 py-5 sm:py-6 text-center text-xs text-slate-500 whitespace-nowrap">—</td>
                            <td class="px-4 sm:px-8 py-5 sm:py-6 text-left whitespace-nowrap">
                                <span class="text-2xl font-black text-emerald-400">{{ number_format($netProfit ?? 0) }}</span>
                            </td>
                        </tr>

                        <tr class="bg-amber-50/80 border-t-2 border-amber-200">
                            <td class="px-4 sm:px-8 py-4 font-black text-amber-900 whitespace-nowrap" colspan="4">
                                <span class="text-sm flex items-center gap-2">
                                    <i class="ti ti-clock-dollar text-amber-600"></i>
                                    سود تحقق‌نیافته — درآمد معوق و پرداخت‌نشده
                                </span>
                                <p class="text-[11px] text-amber-700/80 font-bold mt-1">
                                    مبالغی که هنوز وصول نشده‌اند و پس از تسویه به سود تحقق‌یافته اضافه می‌شوند
                                </p>
                            </td>
                        </tr>

                        @foreach($unrealizedBreakdown ?? [] as $row)
                        <tr class="hover:bg-amber-50/40 transition-colors">
                            <td class="px-4 sm:px-8 py-4 sm:py-5 whitespace-nowrap">
                                <span class="font-black text-amber-900 text-sm">{{ $row['label'] }}</span>
                            </td>
                            <td class="px-4 sm:px-8 py-4 sm:py-5">
                                <p class="text-xs text-slate-500 font-medium leading-relaxed max-w-xl">{{ $row['source'] }}</p>
                            </td>
                            <td class="px-4 sm:px-8 py-4 sm:py-5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-[11px] font-black">
                                    {{ number_format($row['count']) }}
                                    <span class="text-amber-500 font-bold">{{ $row['count_label'] }}</span>
                                </span>
                            </td>
                            <td class="px-4 sm:px-8 py-4 sm:py-5 text-left whitespace-nowrap">
                                <span class="font-black {{ $row['amount'] > 0 ? 'text-amber-700' : 'text-slate-300' }}">{{ number_format($row['amount']) }}</span>
                            </td>
                        </tr>
                        @endforeach

                        <tr class="bg-amber-500 text-white">
                            <td class="px-4 sm:px-8 py-5 sm:py-6 font-black whitespace-nowrap" colspan="2">
                                <span class="text-sm">جمع سود تحقق‌نیافته</span>
                                @php
                                    $unrealizedRows = collect($unrealizedBreakdown ?? []);
                                    $uShop = $unrealizedRows->firstWhere('key', 'shop_unpaid')['amount'] ?? 0;
                                    $uPos = $unrealizedRows->firstWhere('key', 'pos_unpaid')['amount'] ?? 0;
                                    $uManual = $unrealizedRows->firstWhere('key', 'manual_unpaid')['amount'] ?? 0;
                                    $uSvc = $unrealizedRows->firstWhere('key', 'services_unpaid')['amount'] ?? 0;
                                    $uPartial = $unrealizedRows->firstWhere('key', 'services_partial')['amount'] ?? 0;
                                @endphp
                                <p class="text-[11px] text-amber-100 font-bold mt-1">
                                    {{ number_format($uShop) }}
                                    + {{ number_format($uPos + $uManual) }}
                                    + {{ number_format($uSvc) }}
                                    @if($uPartial > 0)
                                        + {{ number_format($uPartial) }} (جزئی)
                                    @endif
                                    = {{ number_format($unrealizedProfit ?? 0) }}
                                </p>
                            </td>
                            <td class="px-4 sm:px-8 py-5 sm:py-6 text-center text-xs text-amber-100 whitespace-nowrap">—</td>
                            <td class="px-4 sm:px-8 py-5 sm:py-6 text-left whitespace-nowrap">
                                <span class="text-2xl font-black text-white">{{ number_format($unrealizedProfit ?? 0) }}</span>
                            </td>
                        </tr>

                        <tr class="bg-indigo-950 text-white">
                            <td class="px-4 sm:px-8 py-5 sm:py-6 font-black whitespace-nowrap" colspan="2">
                                <span class="text-sm">پتانسیل کل سود (تحقق‌یافته + تحقق‌نیافته)</span>
                                <p class="text-[11px] text-indigo-300 font-bold mt-1">
                                    سود تحقق‌یافته {{ number_format($netProfit ?? 0) }} + سود تحقق‌نیافته {{ number_format($unrealizedProfit ?? 0) }}
                                    = {{ number_format(($netProfit ?? 0) + ($unrealizedProfit ?? 0)) }}
                                </p>
                            </td>
                            <td class="px-4 sm:px-8 py-5 sm:py-6 text-center text-xs text-indigo-400 whitespace-nowrap">—</td>
                            <td class="px-4 sm:px-8 py-5 sm:py-6 text-left whitespace-nowrap">
                                <span class="text-2xl font-black text-indigo-300">{{ number_format(($netProfit ?? 0) + ($unrealizedProfit ?? 0)) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoices Section -->
        <div class="space-y-6 animate-slide-up mb-16">
            <h2 class="text-xl font-black text-slate-800 flex items-center gap-3">
                <i class="ti ti-file-invoice text-indigo-500"></i>
                فاکتورها و اسناد مالی
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <i class="ti ti-tool text-2xl text-emerald-500 mb-3"></i>
                    <h3 class="font-black text-slate-800 mb-1">فاکتور خدمات</h3>
                    <p class="text-xs text-slate-500 mb-4">برای سفارشات تعمیر — از صفحه جزئیات سفارش، دکمه «چاپ فاکتور»</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <i class="ti ti-shopping-cart text-2xl text-blue-500 mb-3"></i>
                    <h3 class="font-black text-slate-800 mb-1">فاکتور فروش</h3>
                    <p class="text-xs text-slate-500 mb-4">برای سفارشات فروشگاه — از لیست سفارشات فروشگاه</p>
                    <a href="{{ route('automation.orders.index') }}" class="text-xs font-bold text-blue-600">مشاهده سفارشات</a>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <i class="ti ti-file-text text-2xl text-amber-500 mb-3"></i>
                    <h3 class="font-black text-slate-800 mb-1">پیش‌فاکتور</h3>
                    <p class="text-xs text-slate-500 mb-4">قبل از تایید نهایی — از «چاپ فاکتور» در سفارش تعمیر</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <i class="ti ti-receipt text-2xl text-slate-500 mb-3"></i>
                    <h3 class="font-black text-slate-800 mb-1">رسید / تحویل</h3>
                    <p class="text-xs text-slate-500 mb-4">رسید پذیرش و برگه تحویل از همان منوی چاپ فاکتور</p>
                </div>
            </div>
        </div>

        <!-- Redesigned Tabs/Sections -->
        <div class="space-y-16">
            <!-- Shop Orders Section -->
            <div class="space-y-6 animate-slide-up" style="animation-delay: 0.4s">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-black text-slate-800 flex items-center gap-3">
                        <i class="ti ti-shopping-cart text-blue-500"></i>
                        آخرین سفارشات فروشگاه
                    </h2>
                    <a href="{{ route('automation.orders.index') }}" class="text-xs font-bold text-blue-600 hover:underline">مشاهده همه سفارشات</a>
                </div>
                <x-enhanced-table icon="ti-receipt" animated responsive>
                    <x-slot name="headers">
                        <th class="px-6 py-4 text-right text-xs font-black text-slate-500">شماره سفارش</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-slate-500">مبلغ (تومان)</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-slate-500">مشتری</th>
                        <th class="px-6 py-4 text-center text-xs font-black text-slate-500">وضعیت</th>
                        <th class="px-6 py-4 text-center text-xs font-black text-slate-500">تاریخ</th>
                    </x-slot>
                    <x-slot name="rows">
                        @forelse($shopOrders as $order)
                        <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                            <td class="px-6 py-4">
                                <span class="font-black text-slate-700"><x-hash-ref :value="$order->order_number" /></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-black text-slate-900">{{ number_format($order->total) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-600">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-enhanced-status-badge :status="$order->status->value" />
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-slate-500 font-bold">
                                {{ jalali_date($order->created_at, 'Y/m/d') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-bold">سفارش فروشگاهی یافت نشد.</td>
                        </tr>
                        @endforelse
                    </x-slot>
                </x-enhanced-table>
                {{ $shopOrders->links() }}
            </div>

            <!-- Repair Orders Section -->
            <div class="space-y-6 animate-slide-up" style="animation-delay: 0.45s">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-black text-slate-800 flex items-center gap-3">
                        <i class="ti ti-tool text-emerald-500"></i>
                        آخرین سفارشات تعمیر
                    </h2>
                    <a href="{{ route('automation.service-orders.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">مشاهده همه سفارشات تعمیر</a>
                </div>
                <x-enhanced-table icon="ti-tool" animated responsive>
                    <x-slot name="headers">
                        <th class="px-6 py-4 text-right text-xs font-black text-slate-500">شماره سفارش</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-slate-500">مبلغ (تومان)</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-slate-500">مشتری</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-slate-500">تعمیرکار</th>
                        <th class="px-6 py-4 text-center text-xs font-black text-slate-500">وضعیت</th>
                        <th class="px-6 py-4 text-center text-xs font-black text-slate-500">تاریخ</th>
                    </x-slot>
                    <x-slot name="rows">
                        @forelse($recentRepairOrders as $order)
                        <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                            <td class="px-6 py-4">
                                <a href="{{ route('automation.service-orders.show', $order) }}" class="font-black text-slate-700 hover:text-emerald-600 transition-colors">
                                    <x-hash-ref :value="$order->order_number" />
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-black text-slate-900">{{ number_format($order->service_cost ?? 0) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-600">{{ $order->customer?->name ?? $order->receiver_name ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-600">{{ $order->technician?->name ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-enhanced-status-badge :status="$order->status->value" />
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-slate-500 font-bold">
                                {{ jalali_date($order->created_at, 'Y/m/d') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-bold">سفارش تعمیری یافت نشد.</td>
                        </tr>
                        @endforelse
                    </x-slot>
                </x-enhanced-table>
                {{ $recentRepairOrders->links() }}
            </div>

            <div class="grid grid-cols-1 gap-16">
                <!-- Sales Transactions -->
                <div class="space-y-6 animate-slide-up" style="animation-delay: 0.5s">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-black text-slate-800 flex items-center gap-3">
                            <i class="ti ti-receipt-2 text-indigo-500"></i>
                            تراکنش‌های فروش حضوری
                        </h2>
                    </div>
                    <x-enhanced-table icon="ti-receipt-2" animated>
                        <x-slot name="headers">
                            <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">تاریخ</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">مبلغ (تومان)</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">شرح تراکنش</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">مشتری</th>
                            <th class="px-8 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">وضعیت</th>
                        </x-slot>

                        <x-slot name="rows">
                            @forelse($sales as $sale)
                            <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-white group-hover:text-blue-500 transition-all shadow-sm border border-transparent group-hover:border-slate-100 group-hover:scale-110">
                                            <i class="ti ti-calendar-event text-xl"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-700">
                                                @if(class_exists('\Morilog\Jalali\Jalalian'))
                                                    {{ \Morilog\Jalali\Jalalian::fromCarbon($sale->transaction_date ?? $sale->created_at)->format('Y/m/d') }}
                                                @else
                                                    {{ ($sale->transaction_date ?? $sale->created_at)->format('Y/m/d') }}
                                                @endif
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-bold">تاریخ تراکنش</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-lg font-black text-slate-900 tracking-tight">{{ number_format($sale->amount) }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold">تومان</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors line-clamp-1" title="{{ $sale->description }}">
                                            {{ $sale->description }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $saleCustomer = $sale->customer ?? $sale->order?->user?->customer;
                                    @endphp
                                    @if($saleCustomer)
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-slate-100 to-slate-200 flex items-center justify-center text-slate-600 font-black text-xs shadow-sm border border-white group-hover:rotate-12 transition-transform">
                                                {{ mb_substr($saleCustomer->name, 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-slate-700 group-hover:text-blue-600 transition-colors">{{ $saleCustomer->name }}</span>
                                                <span class="text-[10px] text-slate-400 font-bold">پروفایل مشتری</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-300 font-medium">—</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @php
                                        $statusMap = [
                                            'completed' => ['label' => 'تکمیل شده', 'color' => 'emerald', 'icon' => 'check'],
                                            'pending' => ['label' => 'در انتظار', 'color' => 'amber', 'icon' => 'clock'],
                                            'cancelled' => ['label' => 'لغو شده', 'color' => 'rose', 'icon' => 'x'],
                                        ];
                                        $st = $statusMap[$sale->status ?? 'completed'] ?? ['label' => 'تکمیل شده', 'color' => 'emerald', 'icon' => 'check'];
                                    @endphp
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-{{ $st['color'] }}-50 text-{{ $st['color'] }}-600 text-[11px] font-black border border-{{ $st['color'] }}-100/50 shadow-sm group-hover:scale-110 transition-transform">
                                        <i class="ti ti-{{ $st['icon'] }} text-sm"></i>
                                        {{ $st['label'] }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-8 text-slate-400 font-bold">تراکنشی یافت نشد</td></tr>
                            @endforelse
                        </x-slot>
                    </x-enhanced-table>
                    {{ $sales->links() }}
                </div>

                <!-- Service Transactions -->
                <div class="space-y-6 animate-slide-up" style="animation-delay: 0.5s">
                    <x-enhanced-table title="آخرین تراکنش‌های خدمات" icon="ti-tool" animated>
                        <x-slot name="headers">
                            <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">تاریخ</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">مبلغ (تومان)</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">شرح خدمات</th>
                            <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">تعمیرکار</th>
                            <th class="px-8 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">وضعیت پرداخت</th>
                            <th class="px-8 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">سفارش سرویس</th>
                        </x-slot>

                        <x-slot name="rows">
                            @forelse($services as $service)
                            <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-white group-hover:text-emerald-500 transition-all shadow-sm border border-transparent group-hover:border-slate-100 group-hover:scale-110">
                                            <i class="ti ti-calendar-event text-xl"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-700">
                                                @if(class_exists('\Morilog\Jalali\Jalalian'))
                                                    {{ \Morilog\Jalali\Jalalian::fromCarbon($service->transaction_date ?? $service->created_at)->format('Y/m/d') }}
                                                @else
                                                    {{ ($service->transaction_date ?? $service->created_at)->format('Y/m/d') }}
                                                @endif
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-bold">تاریخ خدمات</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-lg font-black text-slate-900 tracking-tight">{{ number_format($service->amount) }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold">تومان</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                                        @php
                                            $serviceDescription = $service->serviceOrder
                                                ? 'درآمد خدمات تعمیر سفارش '.$service->serviceOrder->order_number
                                                : $service->description;
                                        @endphp
                                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors line-clamp-1" title="{{ $serviceDescription }}">
                                            {{ $serviceDescription }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @if($service->technician)
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm group-hover:rotate-12">
                                                <i class="ti ti-user-cog text-lg"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-slate-700">{{ $service->technician->name }}</span>
                                                <span class="text-[10px] text-slate-400 font-bold">تکنسین</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-300 font-medium">—</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @php
                                        $payStatusMap = [
                                            'paid' => ['label' => 'پرداخت شده', 'color' => 'emerald', 'icon' => 'check'],
                                            'partial' => ['label' => 'پرداخت جزئی', 'color' => 'amber', 'icon' => 'alert-triangle'],
                                            'unpaid' => ['label' => 'پرداخت نشده', 'color' => 'rose', 'icon' => 'x'],
                                        ];
                                        $pst = $payStatusMap[$service->payment_status ?? 'paid'] ?? ['label' => 'پرداخت شده', 'color' => 'emerald', 'icon' => 'check'];
                                    @endphp
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-{{ $pst['color'] }}-50 text-{{ $pst['color'] }}-600 text-[11px] font-black border border-{{ $pst['color'] }}-100/50 shadow-sm group-hover:scale-110 transition-transform">
                                        <i class="ti ti-{{ $pst['icon'] }} text-sm"></i>
                                        {{ $pst['label'] }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @if($service->serviceOrder)
                                        <a href="{{ route('automation.service-orders.show', $service->serviceOrder) }}" class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-600 px-4 py-2 rounded-2xl text-[11px] font-black border border-indigo-100/50 hover:bg-indigo-600 hover:text-white transition-all shadow-sm group/btn">
                                            <i class="ti ti-clipboard-list text-sm group-hover/btn:scale-110 transition-transform"></i>
                                            <x-hash-ref :value="$service->serviceOrder->id" />
                                        </a>
                                    @else
                                        <span class="text-slate-300 font-medium">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <div class="flex flex-col items-center gap-8 animate-fade-in">
                                        <div class="w-28 h-28 rounded-full bg-slate-50 flex items-center justify-center text-slate-200 shadow-inner group">
                                            <i class="ti ti-tools-off text-6xl opacity-40 group-hover:scale-110 transition-transform"></i>
                                        </div>
                                        <div class="space-y-3">
                                            <p class="text-slate-900 text-2xl font-black">تراکنشی یافت نشد</p>
                                            <p class="text-slate-400 font-medium max-w-xs mx-auto">هیچ تراکنش خدماتی در سیستم ثبت نشده است.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </x-slot>

                        @if($services->hasPages())
                            <x-slot name="pagination">
                                <div class="px-8 py-6 bg-slate-50/30">
                                    {{ $services->links() }}
                                </div>
                            </x-slot>
                        @endif
                    </x-enhanced-table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
