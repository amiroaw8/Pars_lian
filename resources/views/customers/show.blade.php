@extends('layouts.admin')

@section('title', 'مشاهده مشتری - ' . $customer->name)

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-black rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-slate-900/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-user text-primary-400"></i>
                        جزئیات اطلاعات مشتری
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">{{ $customer->name }}</h2>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-slate-300">
                        <span class="flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-sm">
                            <i class="ti ti-calendar text-primary-400"></i>
                            عضویت: {{ jalali_date($customer->created_at, 'Y/m/d') }}
                        </span>
                        <span class="flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-sm">
                            <i class="ti ti-hash text-primary-400"></i>
                            شناسه: <x-hash-ref :value="$customer->id" />
                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 shrink-0 customer-show-header-actions">
                    <a href="{{ route('automation.customers.edit', $customer) }}" class="btn-modern btn-modern-warning header-action-btn header-action-btn-warning py-3 px-6 md:py-4 md:px-8 group whitespace-nowrap">
                        <i class="ti ti-edit text-lg group-hover:rotate-12 transition-transform"></i>
                        <span>ویرایش پروفایل</span>
                    </a>
                    <a href="{{ route('automation.customers.index') }}" class="btn-modern btn-modern-light header-action-btn header-action-btn-light py-3 px-6 md:py-4 md:px-8 group whitespace-nowrap">
                        <i class="ti ti-arrow-right text-lg group-hover:-translate-x-1 transition-transform"></i>
                        <span>بازگشت به لیست</span>
                    </a>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-primary-500/20 transition-colors duration-700"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Customer Info & Quick Actions -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Profile Card -->
                <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] p-8 border border-slate-200/60 shadow-xl shadow-slate-200/40 animate-slide-up">
                    <div class="flex flex-col items-center text-center pb-8 mb-8 border-b border-slate-100">
                        <div class="relative group">
                            <div class="absolute inset-0 bg-primary-500 rounded-[2rem] blur-2xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
                            <div class="relative w-28 h-28 rounded-[2rem] bg-gradient-to-br from-primary-500 to-indigo-600 text-white flex items-center justify-center text-4xl font-black shadow-2xl group-hover:scale-105 transition-transform duration-500">
                                {{ mb_substr($customer->name, 0, 1) }}

                            </div>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 mt-6 mb-2">{{ $customer->name }}</h3>
                        <p class="text-slate-400 font-medium text-sm">مشتری وفادار پارس لیان</p>
                    </div>

                    <div class="space-y-6">
                        <div class="group">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 px-1">اطلاعات تماس</label>
                            <a href="tel:{{ $customer->phone }}" class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 group-hover:border-primary-200 group-hover:bg-white transition-all duration-300">
                                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-primary-500 group-hover:bg-primary-500 group-hover:text-white transition-all">
                                    <i class="ti ti-phone text-lg"></i>
                                </div>
                                <span class="text-slate-700 font-black tracking-wider text-lg">{{ $customer->phone }}</span>
                            </a>
                        </div>

                        <div class="group">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2 px-1">آدرس پستی</label>
                            <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 group-hover:border-primary-200 group-hover:bg-white transition-all duration-300">
                                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-primary-500 group-hover:bg-primary-500 group-hover:text-white transition-all flex-shrink-0">
                                    <i class="ti ti-map-pin text-lg"></i>
                                </div>
                                <span class="text-slate-600 font-medium text-sm leading-relaxed">{{ $customer->address ?? 'آدرسی ثبت نشده است' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="p-5 rounded-[2rem] bg-primary-50/50 border border-primary-100/50 text-center group hover:bg-primary-500 hover:scale-105 transition-all duration-500">
                            <div class="text-3xl font-black text-primary-600 group-hover:text-white transition-colors">{{ count($customer->devices) }}</div>
                            <div class="text-[10px] font-bold text-primary-400 group-hover:text-primary-100 uppercase tracking-tighter">دستگاه‌ها</div>
                        </div>
                        <div class="p-5 rounded-[2rem] bg-indigo-50/50 border border-indigo-100/50 text-center group hover:bg-indigo-500 hover:scale-105 transition-all duration-500">
                            <div class="text-3xl font-black text-indigo-600 group-hover:text-white transition-colors">{{ count($customer->serviceOrders) }}</div>
                            <div class="text-[10px] font-bold text-indigo-400 group-hover:text-indigo-100 uppercase tracking-tighter">سفارشات</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] p-8 border border-slate-200/60 shadow-xl shadow-slate-200/40 animate-slide-up animation-delay-200">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                            <i class="ti ti-bolt text-xl"></i>
                        </div>
                        <h4 class="text-lg font-black text-slate-800 tracking-tight">عملیات سریع</h4>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <a href="{{ route('automation.service-orders.create') }}?customer_id={{ $customer->id }}" class="btn-modern btn-modern-primary w-full py-4 justify-center group">
                            <i class="ti ti-plus group-hover:rotate-90 transition-transform"></i>
                            <span>ثبت سفارش جدید</span>
                        </a>
                        <a href="{{ route('automation.devices.create') }}?customer_id={{ $customer->id }}" class="btn-modern btn-modern-secondary w-full py-4 justify-center group">
                            <i class="ti ti-device-laptop group-hover:scale-110 transition-transform"></i>
                            <span>افزودن دستگاه جدید</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content: Devices & Orders -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Financial Summary -->
                <div id="customer-financial-section" class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] p-8 border border-slate-200/60 shadow-xl animate-slide-up scroll-mt-28">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                            <i class="ti ti-wallet text-xl"></i>
                        </div>
                        <h4 class="text-lg font-black text-slate-800">پرداختی‌ها و بدهکاری‌ها</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                            <div class="text-[10px] font-bold text-emerald-600 uppercase mb-1">خدمات (تعمیرات)</div>
                            <div class="text-lg font-black text-slate-800">{{ number_format($financialSummary['service_total'] ?? 0) }} <span class="text-xs font-normal">تومان</span></div>
                            <div class="text-xs text-rose-600 mt-2 font-bold">بدهی: {{ number_format($financialSummary['service_debt'] ?? 0) }} تومان</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100">
                            <div class="text-[10px] font-bold text-blue-600 uppercase mb-1">فروش (مستقیم / فروشگاه)</div>
                            <div class="text-lg font-black text-slate-800">{{ number_format($financialSummary['sales_total'] ?? 0) }} <span class="text-xs font-normal">تومان</span></div>
                            <div class="text-xs text-rose-600 mt-2 font-bold">بدهی: {{ number_format($financialSummary['sales_debt'] ?? 0) }} تومان</div>
                            <div class="text-xs text-slate-500 mt-1">سفارشات فروشگاه: {{ $financialSummary['shop_orders'] ?? 0 }}</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100">
                            <div class="text-[10px] font-bold text-rose-600 uppercase mb-1">جمع بدهی فعلی</div>
                            <div class="text-2xl font-black text-rose-700">{{ number_format($debtSummary['total_debt'] ?? 0) }} <span class="text-xs font-normal">تومان</span></div>
                            <div class="text-xs text-slate-500 mt-2">پرداخت‌شده: {{ number_format($debtSummary['total_paid'] ?? 0) }} تومان</div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h5 class="text-sm font-black text-slate-700 mb-3 flex items-center gap-2">
                            <i class="ti ti-list-check text-primary-500"></i>
                            وضعیت پرداخت و بدهکاری
                        </h5>
                        @if(($orderPaymentStatuses ?? collect())->isNotEmpty())
                            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-slate-50 text-slate-500 text-[10px] font-black uppercase">
                                            <th class="px-4 py-3 text-right">تاریخ</th>
                                            <th class="px-4 py-3 text-right">بخش</th>
                                            <th class="px-4 py-3 text-right">مرجع</th>
                                            <th class="px-4 py-3 text-right">مبلغ</th>
                                            <th class="px-4 py-3 text-right">وضعیت</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($orderPaymentStatuses as $row)
                                            @php
                                                $statusLabel = match($row['payment_status']) {
                                                    'paid' => 'پرداخت شده',
                                                    'debt' => 'بدهکار',
                                                    'pending' => 'در انتظار پرداخت',
                                                    default => $row['payment_status'],
                                                };
                                                $statusClass = match($row['payment_status']) {
                                                    'paid' => 'bg-emerald-100 text-emerald-700',
                                                    'debt' => 'bg-rose-100 text-rose-700',
                                                    'pending' => 'bg-amber-100 text-amber-700',
                                                    default => 'bg-slate-100 text-slate-600',
                                                };
                                            @endphp
                                            <tr class="hover:bg-slate-50/80">
                                                <td class="px-4 py-3 whitespace-nowrap text-slate-600 font-bold">
                                                    {{ jalali_date($row['date']) }}
                                                </td>
                                                <td class="px-4 py-3 text-slate-600 font-bold">{{ $row['category_label'] }}</td>
                                                <td class="px-4 py-3 text-xs whitespace-nowrap">
                                                    @if(!empty($row['reference_url']))
                                                        <a href="{{ $row['reference_url'] }}" class="text-primary-600 font-bold hover:text-primary-700 hover:underline">
                                                            {{ $row['reference'] }}

                                                        </a>
                                                    @else
                                                        <span class="text-slate-500">{{ $row['reference'] ?? '—' }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 font-black text-slate-800 whitespace-nowrap">
                                                    {{ number_format($row['amount']) }} تومان
                                                    @if(($row['payment_status'] ?? '') === 'debt' && ($row['total_amount'] ?? 0) > ($row['debt_amount'] ?? 0))
                                                        <span class="block text-[10px] font-normal text-slate-400">مبلغ کل: {{ number_format($row['total_amount']) }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-black {{ $statusClass }}">{{ $statusLabel }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-slate-500 text-center py-6 rounded-2xl bg-slate-50 border border-dashed border-slate-200">سفارش مالی ثبت نشده است.</p>
                        @endif
                    </div>

                    <div class="mb-6">
                        <h5 class="text-sm font-black text-slate-700 mb-3 flex items-center gap-2">
                            <i class="ti ti-history text-primary-500"></i>
                            تاریخچه پرداخت و بدهکاری‌ها
                        </h5>
                        @if(($financialHistory ?? collect())->isNotEmpty())
                            <div class="overflow-x-auto overflow-y-auto max-h-[28rem] rounded-2xl border border-slate-200">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-slate-50 text-slate-500 text-[10px] font-black uppercase">
                                            <th class="px-4 py-3 text-right">تاریخ</th>
                                            <th class="px-4 py-3 text-right">نوع</th>
                                            <th class="px-4 py-3 text-right">بخش</th>
                                            <th class="px-4 py-3 text-right">مبلغ</th>
                                            <th class="px-4 py-3 text-right">شرح</th>
                                            <th class="px-4 py-3 text-right">مرجع</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($financialHistory as $row)
                                            @php
                                                $typeLabel = match($row['type']) {
                                                    'payment' => 'پرداخت',
                                                    'debt' => 'بدهی',
                                                    'proforma' => 'پیش‌فاکتور',
                                                    default => $row['type'],
                                                };
                                                $typeClass = match($row['type']) {
                                                    'payment' => 'bg-emerald-100 text-emerald-700',
                                                    'debt' => 'bg-rose-100 text-rose-700',
                                                    'proforma' => 'bg-amber-100 text-amber-700',
                                                    default => 'bg-slate-100 text-slate-600',
                                                };
                                                $categoryLabel = ($row['category'] ?? '') === 'service' ? 'خدمات' : 'فروش';
                                            @endphp
                                            <tr class="hover:bg-slate-50/80 {{ !empty($row['is_open_balance']) ? 'bg-rose-50/40' : '' }}">
                                                <td class="px-4 py-3 whitespace-nowrap text-slate-600 font-bold">
                                                    {{ jalali_date($row['date']) }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-black {{ $typeClass }}">{{ $typeLabel }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-slate-600 font-bold">{{ $categoryLabel }}</td>
                                                <td class="px-4 py-3 font-black text-slate-800 whitespace-nowrap">{{ number_format($row['amount']) }} تومان</td>
                                                <td class="px-4 py-3 text-slate-600 max-w-[200px] truncate" title="{{ $row['description'] }}">{{ $row['description'] }}</td>
                                                <td class="px-4 py-3 text-xs whitespace-nowrap">
                                                    @if(!empty($row['reference_url']))
                                                        <a href="{{ $row['reference_url'] }}" class="text-primary-600 font-bold hover:text-primary-700 hover:underline">
                                                            {{ $row['reference'] }}

                                                        </a>
                                                    @else
                                                        <span class="text-slate-500">{{ $row['reference'] ?? '—' }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-slate-500 text-center py-6 rounded-2xl bg-slate-50 border border-dashed border-slate-200">هنوز تراکنش مالی برای این مشتری ثبت نشده است.</p>
                        @endif
                    </div>

                    <p class="text-[10px] text-slate-400 mb-4">ثبت دستی پرداخت/بدهی از فرم زیر یا POS و سفارش تعمیر انجام می‌شود.</p>

                    @if(auth()->user()->canManageAccounting() || auth()->user()->canManageCustomers() || auth()->user()->isAdmin())
                    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-5 items-stretch">
                        
                        <form action="{{ route('automation.customers.financial-transaction.store', $customer) }}" method="POST" class="customer-financial-form flex flex-col h-full p-6 rounded-[1.75rem] bg-gradient-to-br from-emerald-50/90 via-white to-white border border-emerald-200/80 shadow-sm shadow-emerald-100/40">
                            @csrf
                            <input type="hidden" name="record_type" value="payment">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-emerald-200/70">
                                <div class="w-11 h-11 shrink-0 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25">
                                    <i class="ti ti-cash text-xl"></i>
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-sm font-black text-emerald-900">ثبت پرداخت</h5>
                                    <p class="text-[11px] text-emerald-700/75 leading-relaxed">مبلغ دریافتی از مشتری</p>
                                </div>
                            </div>
                            <div class="space-y-4 flex-1">
                                <div>
                                    <label class="block text-[11px] font-black text-emerald-800/80 uppercase tracking-wide mb-1.5">بخش</label>
                                    <select name="category" class="form-control text-sm w-full customer-financial-category" data-form="payment" required>
                                        <option value="service">خدمات (تعمیرات)</option>
                                        <option value="sales">فروش</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-emerald-800/80 uppercase tracking-wide mb-1.5">مبلغ (تومان)</label>
                                    <x-money-field
                                        name="amount"
                                        id="customer-financial-payment-amount"
                                        wordsId="money-words-customer-financial-payment-amount"
                                        placeholder="مثلاً ۵٬۰۰۰٬۰۰۰"
                                        :required="true"
                                        class="form-control text-sm w-full !py-2.5 !px-3 !rounded-xl !font-bold !bg-white"
                                    />
                                </div>
                                <div class="customer-financial-service-only hidden" data-form="payment">
                                    <label class="block text-[11px] font-black text-emerald-800/80 uppercase tracking-wide mb-1.5">سفارش تعمیر</label>
                                    <select name="service_order_id" class="form-control text-sm w-full customer-financial-service-select" disabled>
                                        <option value="">— تسویه خودکار بدهی —</option>
                                        @foreach($customerServiceOrders ?? [] as $so)
                                            <option value="{{ $so->id }}"><x-hash-ref :value="$so->id" /> — بدهی: {{ number_format($so->debt_amount ?? 0) }} ت</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed">برای تسویه بدهی یک سفارش تعمیر مشخص، آن را انتخاب کنید.</p>
                                </div>
                                <div class="customer-financial-sales-only hidden" data-form="payment">
                                    <label class="block text-[11px] font-black text-emerald-800/80 uppercase tracking-wide mb-1.5">سفارش فروش</label>
                                    <select name="order_id" class="form-control text-sm w-full customer-financial-shop-select" disabled>
                                        <option value="">— انتخاب سفارش —</option>
                                        @foreach($customerShopOrders ?? [] as $shopOrder)
                                            <option value="{{ $shopOrder->id }}">
                                                {{ $shopOrder->order_number }} — {{ number_format($shopOrder->total) }} ت
                                                @if($shopOrder->hasOutstandingDebt())
                                                    (بدهی باز)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed">سفارش فروشگاه یا POS مرتبط با این پرداخت را انتخاب کنید.</p>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-emerald-800/80 uppercase tracking-wide mb-1.5">شرح (اختیاری)</label>
                                    <input type="text" name="description" class="form-control text-sm w-full" placeholder="مثلاً پرداخت نقدی">
                                </div>
                            </div>
                            <div class="pt-4 mt-auto space-y-3 border-t border-emerald-200/60">
                                <label class="flex items-start gap-2.5 text-xs text-slate-600 cursor-pointer">
                                    <input type="checkbox" name="confirm" value="1" required class="mt-0.5 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                                    <span>صحت اطلاعات را تایید می‌کنم.</span>
                                </label>
                                <button type="submit" class="btn-modern btn-sm w-full justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600 shadow-md shadow-emerald-600/20">
                                    <i class="ti ti-check"></i>
                                    ثبت پرداخت
                                </button>
                            </div>
                        </form>

                        
                        <form action="{{ route('automation.customers.financial-transaction.store', $customer) }}" method="POST" class="customer-financial-form flex flex-col h-full p-6 rounded-[1.75rem] bg-gradient-to-br from-rose-50/90 via-white to-white border border-rose-200/80 shadow-sm shadow-rose-100/40">
                            @csrf
                            <input type="hidden" name="record_type" value="debt">
                            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-rose-200/70">
                                <div class="w-11 h-11 shrink-0 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/25">
                                    <i class="ti ti-receipt-2 text-xl"></i>
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-sm font-black text-rose-900">ثبت بدهی</h5>
                                    <p class="text-[11px] text-rose-700/75 leading-relaxed">مبلغی که مشتری هنوز پرداخت نکرده</p>
                                </div>
                            </div>
                            <div class="space-y-4 flex-1">
                                <div>
                                    <label class="block text-[11px] font-black text-rose-800/80 uppercase tracking-wide mb-1.5">بخش</label>
                                    <select name="category" class="form-control text-sm w-full customer-financial-category" data-form="debt" required>
                                        <option value="service">خدمات (تعمیرات)</option>
                                        <option value="sales">فروش</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-rose-800/80 uppercase tracking-wide mb-1.5">مبلغ (تومان)</label>
                                    <x-money-field
                                        name="amount"
                                        id="customer-financial-debt-amount"
                                        wordsId="money-words-customer-financial-debt-amount"
                                        placeholder="مثلاً ۳۸٬۷۲۰٬۰۰۰"
                                        :required="true"
                                        class="form-control text-sm w-full !py-2.5 !px-3 !rounded-xl !font-bold !bg-white"
                                    />
                                </div>
                                <div class="customer-financial-service-only hidden" data-form="debt">
                                    <label class="block text-[11px] font-black text-rose-800/80 uppercase tracking-wide mb-1.5">
                                        سفارش تعمیر <span class="text-rose-600 normal-case">*</span>
                                    </label>
                                    <select name="service_order_id" class="form-control text-sm w-full customer-financial-service-select" disabled>
                                        <option value="">— انتخاب کنید —</option>
                                        @foreach($customerServiceOrders ?? [] as $so)
                                            <option value="{{ $so->id }}"><x-hash-ref :value="$so->id" /> — {{ number_format($so->service_cost ?? 0) }} ت</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed">برای ثبت بدهی تعمیر، سفارش مربوطه را انتخاب کنید.</p>
                                </div>
                                <div class="customer-financial-sales-only hidden" data-form="debt">
                                    <label class="block text-[11px] font-black text-rose-800/80 uppercase tracking-wide mb-1.5">سفارش فروش</label>
                                    <select name="order_id" class="form-control text-sm w-full customer-financial-shop-select" disabled>
                                        <option value="">— انتخاب سفارش —</option>
                                        @foreach($customerShopOrders ?? [] as $shopOrder)
                                            <option value="{{ $shopOrder->id }}">
                                                {{ $shopOrder->order_number }} — {{ number_format($shopOrder->total) }} ت
                                                @if($shopOrder->hasOutstandingDebt())
                                                    (بدهی باز)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed">بدهی را به سفارش فروشگاه یا POS مشخص متصل کنید.</p>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-rose-800/80 uppercase tracking-wide mb-1.5">شرح (اختیاری)</label>
                                    <input type="text" name="description" class="form-control text-sm w-full" placeholder="مثلاً هزینه تعمیر">
                                </div>
                            </div>
                            <div class="pt-4 mt-auto space-y-3 border-t border-rose-200/60">
                                <label class="flex items-start gap-2.5 text-xs text-slate-600 cursor-pointer">
                                    <input type="checkbox" name="confirm" value="1" required class="mt-0.5 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                    <span>صحت اطلاعات را تایید می‌کنم.</span>
                                </label>
                                <button type="submit" class="btn-modern btn-sm w-full justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white border-rose-600 shadow-md shadow-rose-600/20">
                                    <i class="ti ti-plus"></i>
                                    ثبت بدهی
                                </button>
                            </div>
                        </form>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-3">
                        <i class="ti ti-info-circle"></i>
                        پیش‌فاکتور (پیشنهاد قیمت قبل از فروش) از منوی
                        <a href="{{ route('automation.accounting.proforma.create') }}" class="text-primary-600 font-bold hover:underline">حسابداری</a>
                        یا صفحه سفارش تعمیر صادر می‌شود و در اینجا ثبت نمی‌شود.
                    </p>
                    <script>
                        document.querySelectorAll('.customer-financial-category').forEach(function (select) {
                            var form = select.closest('.customer-financial-form');
                            var formId = select.dataset.form;
                            if (!form) return;

                            var serviceBlock = form.querySelector('.customer-financial-service-only[data-form="' + formId + '"]');
                            var salesBlock = form.querySelector('.customer-financial-sales-only[data-form="' + formId + '"]');
                            var serviceSelect = serviceBlock ? serviceBlock.querySelector('select[name="service_order_id"]') : null;
                            var shopSelect = salesBlock ? salesBlock.querySelector('select[name="order_id"]') : null;

                            function toggleCategoryFields() {
                                var isService = select.value === 'service';

                                if (serviceBlock) {
                                    serviceBlock.classList.toggle('hidden', !isService);
                                }
                                if (salesBlock) {
                                    salesBlock.classList.toggle('hidden', isService);
                                }
                                if (serviceSelect) {
                                    serviceSelect.disabled = !isService;
                                    serviceSelect.required = isService && formId === 'debt';
                                    if (!isService) {
                                        serviceSelect.value = '';
                                    }
                                }
                                if (shopSelect) {
                                    shopSelect.disabled = isService;
                                    if (isService) {
                                        shopSelect.value = '';
                                    }
                                }
                            }

                            select.addEventListener('change', toggleCategoryFields);
                            toggleCategoryFields();
                        });
                    </script>
                    @endif
                </div>

                <!-- Shop / POS Orders -->
                <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] p-8 border border-slate-200/60 shadow-xl shadow-slate-200/40 animate-slide-up animation-delay-250">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-violet-500/10 text-violet-600 flex items-center justify-center">
                                <i class="ti ti-shopping-cart text-xl"></i>
                            </div>
                            <h4 class="text-lg font-black text-slate-800 tracking-tight">سفارشات فروشگاه و حضوری</h4>
                        </div>
                        <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-black">{{ count($customerShopOrders ?? []) }} مورد</span>
                    </div>
                    @if(!empty($customerShopOrders) && count($customerShopOrders) > 0)
                        <div class="space-y-3">
                            @foreach($customerShopOrders as $shopOrder)
                                <a href="{{ route('automation.orders.show', $shopOrder) }}" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-violet-200 hover:bg-white transition-all">
                                    <div>
                                        <span class="font-black text-slate-800">{{ $shopOrder->order_number }}</span>
                                        <span class="text-xs text-slate-500 block mt-1">{{ jalali_date($shopOrder->created_at, 'Y/m/d H:i') }}</span>
                                    </div>
                                    <div class="text-left">
                                        <span class="font-black text-slate-800">{{ number_format($shopOrder->total) }} تومان</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $shopOrder->notes ? 'POS / فروشگاه' : 'فروشگاه' }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 text-center py-6">سفارش فروشگاهی برای این مشتری ثبت نشده است.</p>
                    @endif

                    <div class="mt-8 pt-8 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                                    <i class="ti ti-tool text-lg"></i>
                                </div>
                                <h5 class="text-base font-black text-slate-800 tracking-tight">سفارشات تعمیر</h5>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-black">{{ count($customer->serviceOrders ?? []) }} مورد</span>
                        </div>

                        @if($customer->serviceOrders && count($customer->serviceOrders) > 0)
                            <div class="space-y-3">
                                @foreach($customer->serviceOrders as $serviceOrder)
                                    <a href="{{ route('automation.service-orders.show', $serviceOrder) }}" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:bg-white transition-all">
                                        <div class="min-w-0">
                                            <span class="font-black text-slate-800"><x-hash-ref :value="$serviceOrder->id" /></span>
                                            @if($serviceOrder->device)
                                                <span class="text-xs text-slate-500 block mt-1">{{ $serviceOrder->device->type }} — {{ $serviceOrder->device->model }}</span>
                                            @endif
                                            <span class="text-xs text-slate-400 block mt-1">{{ jalali_date($serviceOrder->created_at, 'Y/m/d H:i') }}</span>
                                        </div>
                                        <div class="text-left shrink-0 mr-4">
                                            @if((float) ($serviceOrder->service_cost ?? 0) > 0)
                                                <span class="font-black text-slate-800">{{ number_format($serviceOrder->service_cost) }} تومان</span>
                                            @endif
                                            <span class="block mt-1">
                                                <x-enhanced-status-badge :status="$serviceOrder->status ?? 'registered'" />
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-500 text-center py-6">سفارش تعمیر برای این مشتری ثبت نشده است.</p>
                        @endif
                    </div>
                </div>

                <!-- Order History -->
                <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] p-8 border border-slate-200/60 shadow-xl shadow-slate-200/40 animate-slide-up animation-delay-300">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                                <i class="ti ti-history text-xl"></i>
                            </div>
                            <h4 class="text-lg font-black text-slate-800 tracking-tight">تاریخچه سفارشات</h4>
                        </div>
                        <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-black">{{ count($customer->serviceOrders) }} سفارش</span>
                    </div>

                    @if($customer->serviceOrders && count($customer->serviceOrders) > 0)
                        <div class="overflow-hidden rounded-[2rem] border border-slate-100 bg-white">
                            <x-enhanced-table striped hover responsive>
                                <x-slot name="headers">
                                    <th class="py-5 px-6 text-right">کد پیگیری</th>
                                    <th class="py-5 px-6 text-right">دستگاه</th>
                                    <th class="py-5 px-6 text-right">وضعیت</th>
                                    <th class="py-5 px-6 text-right">تاریخ ثبت</th>
                                    <th class="py-5 px-6 text-center">عملیات</th>
                                </x-slot>
                                <x-slot name="rows">
                                    @foreach($customer->serviceOrders as $order)
                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-2 h-8 rounded-full bg-primary-500 scale-y-0 group-hover:scale-y-100 transition-transform duration-300"></div>
                                                <span class="font-black text-slate-800"><x-hash-ref :value="$order->id" /></span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-slate-700">{{ $order->device->type }}</span>
                                                <span class="text-[10px] text-slate-400 font-medium">{{ $order->device->model }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <x-enhanced-status-badge :status="$order->status ?? 'registered'" />
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex flex-col text-right">
                                                <span class="text-xs font-black text-slate-600 tracking-tighter">{{ jalali_date($order->created_at, 'Y/m/d') }}</span>
                                                <span class="text-[10px] text-slate-400 font-medium">{{ jalali_date($order->created_at, 'H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <a href="{{ route('automation.service-orders.show', $order) }}" class="inline-flex w-10 h-10 items-center justify-center bg-white shadow-sm border border-slate-100 rounded-xl text-primary-500 hover:bg-primary-500 hover:text-white hover:scale-110 hover:shadow-lg hover:shadow-primary-500/20 transition-all duration-300">
                                                <i class="ti ti-eye text-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </x-slot>
                            </x-enhanced-table>
                        </div>
                    @else
                        <div class="text-center py-16 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                            <div class="w-20 h-20 bg-white rounded-[2rem] shadow-sm flex items-center justify-center mx-auto mb-6 text-slate-300">
                                <i class="ti ti-clipboard-off text-4xl"></i>
                            </div>
                            <h5 class="text-slate-800 font-black mb-2">سفارشی یافت نشد</h5>
                            <p class="text-slate-400 text-sm mb-8">هنوز هیچ سفارشی برای این مشتری در سیستم ثبت نشده است.</p>
                            <a href="{{ route('automation.service-orders.create') }}?customer_id={{ $customer->id }}" class="btn-modern btn-modern-primary group">
                                <i class="ti ti-plus group-hover:rotate-90 transition-transform"></i>
                                <span>ثبت اولین سفارش</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Registered Devices -->
                <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] p-8 border border-slate-200/60 shadow-xl shadow-slate-200/40 animate-slide-up animation-delay-400">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center">
                                <i class="ti ti-device-laptop text-xl"></i>
                            </div>
                            <h4 class="text-lg font-black text-slate-800 tracking-tight">دستگاه‌های ثبت شده</h4>
                        </div>
                        <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-black">{{ count($customer->devices) }} دستگاه</span>
                    </div>

                    @if($customer->devices && count($customer->devices) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($customer->devices as $device)
                                <div class="group relative p-6 rounded-3xl bg-slate-50 border border-slate-100 hover:border-primary-200 hover:bg-white hover:shadow-xl hover:shadow-primary-500/5 transition-all duration-500 overflow-hidden">
                                    <!-- background icon -->
                                    <div class="absolute -right-4 -bottom-4 text-slate-200 opacity-20 group-hover:opacity-40 group-hover:scale-125 transition-all duration-700 pointer-events-none">
                                        <i class="ti ti-device-laptop text-7xl"></i>
                                    </div>

                                    <div class="relative z-10">
                                        <div class="flex items-start justify-between mb-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-slate-400 group-hover:bg-primary-500 group-hover:text-white transition-all duration-500">
                                                    <i class="ti ti-device-laptop text-2xl"></i>
                                                </div>
                                                <div>
                                                    <h5 class="font-black text-slate-800 group-hover:text-primary-600 transition-colors">{{ $device->type }}</h5>
                                                    <p class="text-xs text-slate-400 font-medium">{{ $device->model }}</p>
                                                </div>
                                            </div>
                                            <a href="{{ route('automation.devices.show', $device) }}" class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-primary-600 hover:scale-110 transition-all">
                                                <i class="ti ti-chevron-left"></i>
                                            </a>
                                        </div>

                                        <div class="flex items-center justify-between pt-4 border-t border-slate-200/50">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span class="text-[10px] font-black text-slate-500 uppercase">شناسه دستگاه:</span>
                                            </div>
                                            <span class="text-xs font-black text-slate-700 tracking-wider"><x-hash-ref :value="$device->id" /></span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                            <div class="w-20 h-20 bg-white rounded-[2rem] shadow-sm flex items-center justify-center mx-auto mb-6 text-slate-300">
                                <i class="ti ti-device-laptop-off text-4xl"></i>
                            </div>
                            <h5 class="text-slate-800 font-black mb-2">هیچ دستگاهی ثبت نشده است</h5>
                            <p class="text-slate-400 text-sm mb-8">برای این مشتری هنوز دستگاهی در سیستم تعریف نشده است.</p>
                            <a href="{{ route('automation.devices.create') }}?customer_id={{ $customer->id }}" class="btn-modern btn-modern-primary group">
                                <i class="ti ti-plus group-hover:rotate-90 transition-transform"></i>
                                <span>ثبت اولین دستگاه</span>
                            </a>
                        </div>
                    @endif
                </div>
                <!-- Activity History -->
                <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] p-8 border border-slate-200/60 shadow-xl shadow-slate-200/40 animate-slide-up animation-delay-500">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                                <i class="ti ti-activity text-xl"></i>
                            </div>
                            <h4 class="text-lg font-black text-slate-800 tracking-tight">تاریخچه تغییرات</h4>
                        </div>
                        <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-black">{{ count($customer->activityLogs) }} فعالیت</span>
                    </div>

                    @if($customer->activityLogs && count($customer->activityLogs) > 0)
                        <div class="space-y-6 relative before:absolute before:right-[1.45rem] before:top-2 before:bottom-2 before:w-px before:bg-slate-100">
                            @foreach($customer->activityLogs as $log)
                                <div class="relative pr-12">
                                    <div class="absolute right-0 top-1 w-12 h-12 flex items-center justify-center z-10">
                                        <div @class([
                                            'w-4 h-4 rounded-full border-4 border-white shadow-sm',
                                            'bg-emerald-500' => $log->event === 'created',
                                            'bg-primary-500' => $log->event === 'updated',
                                            'bg-rose-500' => !in_array($log->event, ['created', 'updated']),
                                        ])></div>
                                    </div>
                                    <div class="p-5 rounded-3xl bg-slate-50 border border-slate-100 hover:border-primary-200 hover:bg-white hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-black text-slate-800">
                                                @if($log->event === 'created') ثبت مشتری جدید
                                                @elseif($log->event === 'updated') ویرایش اطلاعات
                                                @else حذف مشتری @endif
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 leading-relaxed mb-3">
                                            توسط <span class="text-primary-600 font-bold">{{ $log->user->name ?? 'سیستم' }}</span>
                                            @if($log->event === 'updated' && !empty($log->new_values))
                                                @php $changes = $log->changeLines(); @endphp
                                                @if(!empty($changes))
                                                <div class="mt-2 p-2 rounded-xl bg-slate-100/50 text-[10px] font-medium space-y-1">
                                                    @foreach($changes as $line)
                                                        <div class="text-slate-600">{{ $line }}</div>
                                                    @endforeach
                                                </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                            <p class="text-slate-400 text-xs font-bold">هیچ تاریخچه‌ای برای این مشتری ثبت نشده است.</p>
                        </div>
                    @endif
                <!-- CRM: Customer Interactions -->
                <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] p-8 border border-slate-200/60 shadow-xl shadow-slate-200/40 animate-slide-up animation-delay-500">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary-500/10 text-primary-600 flex items-center justify-center">
                                <i class="ti ti-messages text-xl"></i>
                            </div>
                            <h4 class="text-lg font-black text-slate-800 tracking-tight">تعاملات و پیگیری‌ها (CRM)</h4>
                        </div>
                        <div class="flex items-center gap-3">
                            @if(count($interactionUsers) > 0)
                                <form action="{{ route('automation.customers.show', $customer) }}" method="GET" class="flex items-center gap-2">
                                    <select name="user_id" onchange="this.form.submit()" class="text-xs px-3 py-2 rounded-xl bg-slate-100 border-none focus:ring-2 focus:ring-primary-500/20 text-slate-600 font-bold outline-none">
                                        <option value="">همه کاربران</option>
                                        @foreach($interactionUsers as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}

                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif
                            <button type="button" onclick="document.getElementById('interactionForm').classList.toggle('hidden')" class="btn-modern btn-modern-primary py-2 px-4 text-xs group">
                                <i class="ti ti-plus group-hover:rotate-90 transition-transform"></i>
                                <span>ثبت تعامل جدید</span>
                            </button>
                        </div>
                    </div>

                    <!-- New Interaction Form (Hidden by default) -->
                    <div id="interactionForm" class="hidden mb-12 p-8 rounded-[2rem] bg-slate-50 border border-slate-200 animate-fade-in">
                        <form action="{{ route('automation.customers.interactions.store', $customer) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-black text-slate-700 mb-2 px-1">نوع تعامل</label>
                                    <select name="type" class="w-full px-5 py-4 rounded-2xl bg-white border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none font-medium text-slate-600 shadow-sm" required>
                                        <option value="note">یادداشت</option>
                                        <option value="call">تماس تلفنی</option>
                                        <option value="meeting">جلسه حضوری</option>
                                        <option value="email">ایمیل</option>
                                        <option value="sms">پیامک</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-black text-slate-700 mb-2 px-1">تاریخ</label>
                                    <input type="datetime-local" name="interaction_date" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-5 py-4 rounded-2xl bg-white border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none font-medium text-slate-600 shadow-sm" required>
                                </div>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-black text-slate-700 mb-2 px-1">جزئیات و توضیحات</label>
                                <textarea name="content" rows="4" class="w-full px-5 py-4 rounded-2xl bg-white border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none font-medium text-slate-600 shadow-sm" placeholder="جزئیات گفتگو یا یادداشت خود را اینجا وارد کنید..." required></textarea>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" onclick="document.getElementById('interactionForm').classList.add('hidden')" class="btn-modern btn-modern-light py-3 px-6">انصراف</button>
                                <button type="submit" class="btn-modern btn-modern-primary py-3 px-8">ذخیره تعامل</button>
                            </div>
                        </form>
                    </div>

                    @if($customer->interactions && count($customer->interactions) > 0)
                        <div class="relative space-y-8 before:absolute before:right-8 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                            @foreach($customer->interactions->sortByDesc('interaction_date') as $interaction)
                                <div class="relative pr-16 group">
                                    <!-- Timeline Dot -->
                                    <div class="absolute right-[1.85rem] top-0 w-4 h-4 rounded-full border-4 border-white bg-primary-500 shadow-sm group-hover:scale-125 transition-transform duration-300 z-10"></div>
                                    
                                    <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100 hover:border-primary-200 hover:bg-white hover:shadow-xl hover:shadow-primary-500/5 transition-all duration-500">
                                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-primary-500">
                                                    <i class="ti {{ $interaction->type_icon }} text-lg"></i>
                                                </div>
                                                <div>
                                                    <h5 class="font-black text-slate-800 text-sm">{{ $interaction->type_label }}</h5>
                                                    <p class="text-[10px] text-slate-400 font-medium">ثبت شده توسط: {{ $interaction->user->name ?? 'سیستم' }}</p>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <span class="text-xs font-black text-slate-600 tracking-tighter">
                                                    {{ jalali_date($interaction->interaction_date, 'Y/m/d') }}
                                                </span>
                                                <span class="text-[10px] text-slate-400 font-medium">
                                                    {{ jalali_date($interaction->interaction_date, 'H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">
                                            {{ $interaction->content }}

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 text-slate-300">
                                <i class="ti ti-messages-off text-3xl"></i>
                            </div>
                            <h5 class="text-slate-800 font-black mb-1 text-sm">هیچ تعاملی ثبت نشده است</h5>
                            <p class="text-slate-400 text-xs mb-6">اولین پیگیری یا یادداشت برای این مشتری را ثبت کنید.</p>
                            <button type="button" onclick="document.getElementById('interactionForm').classList.remove('hidden')" class="btn-modern btn-modern-primary py-2 px-6 text-xs">
                                <span>شروع پیگیری مشتری</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.customer-show-header-actions .header-action-btn {
    position: relative;
    z-index: 20;
    font-weight: 700;
    min-width: 10rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
}
.customer-show-header-actions .header-action-btn-light {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border: 2px solid #e2e8f0 !important;
}
.customer-show-header-actions .header-action-btn-light:hover {
    background-color: #f1f5f9 !important;
    color: #020617 !important;
    border-color: #cbd5e1 !important;
}
.customer-show-header-actions .header-action-btn-warning {
    background-color: #f59e0b !important;
    color: #ffffff !important;
    border: 2px solid #fbbf24 !important;
}
.customer-show-header-actions .header-action-btn-warning:hover {
    background-color: #d97706 !important;
    color: #ffffff !important;
    border-color: #f59e0b !important;
}
</style>
@endsection


