@extends('layouts.app')

@section('title', 'تاریخچه سفارشات')
@section('page_title', 'لیست تمامی سفارشات')

@section('content')
<div class="relative" x-data="{ activeTab: 'repairs' }">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/5 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Tab Navigation -->
        <div class="flex items-center gap-4 bg-white/50 backdrop-blur-sm p-2 rounded-[2rem] border border-white/50 shadow-sm w-fit mx-auto">
            <button 
                @click="activeTab = 'repairs'" 
                :class="activeTab === 'repairs' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-slate-500 hover:bg-white'"
                class="px-8 py-3 rounded-2xl text-sm font-black transition-all duration-300 flex items-center gap-2"
            >
                <i class="ti ti-tool"></i>
                سفارشات تعمیرات
            </button>
            <button 
                @click="activeTab = 'shop'" 
                :class="activeTab === 'shop' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/20' : 'text-slate-500 hover:bg-white'"
                class="px-8 py-3 rounded-2xl text-sm font-black transition-all duration-300 flex items-center gap-2"
            >
                <i class="ti ti-shopping-cart"></i>
                سفارشات فروشگاه
            </button>
        </div>

        <!-- Repairs Section -->
        <div x-show="activeTab === 'repairs'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <x-enhanced-card icon="clipboard-list">
                <x-slot name="title">
                    <div class="flex items-center gap-4">
                        <span class="text-xl font-black text-slate-900">سفارشات تعمیرات</span>
                        <div class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">
                            {{ $repairOrders->total() }} سفارش
                        </div>
                    </div>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-separate border-spacing-y-4">
                        <thead>
                            <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                                <th class="px-6 py-2">کد پیگیری</th>
                                <th class="px-6 py-2">دستگاه و مدل</th>
                                <th class="px-6 py-2">مشکل اعلامی</th>
                                <th class="px-6 py-2 text-center">وضعیت</th>
                                <th class="px-6 py-2">تاریخ ثبت</th>
                                <th class="px-6 py-2 text-center">هزینه نهایی</th>
                                <th class="px-6 py-2 text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($repairOrders as $order)
                            <tr class="group hover:bg-slate-50/80 transition-all duration-500">
                                <td class="px-6 py-5 first:rounded-r-[2rem] bg-white group-hover:bg-transparent transition-colors">
                                    <span class="font-black text-slate-900 bg-slate-100 px-4 py-2 rounded-xl group-hover:bg-white transition-colors"><x-hash-ref :value="$order->id" /></span>
                                </td>
                                <td class="px-6 py-5 bg-white group-hover:bg-transparent transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-sm">
                                            <i class="ti ti-device-laptop text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-sm group-hover:text-primary-600 transition-colors">{{ $order->device->type ?? 'نامشخص' }}</div>
                                            <div class="text-[10px] text-slate-400 font-black uppercase tracking-wider">{{ $order->device->model ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 bg-white group-hover:bg-transparent transition-colors">
                                    <div class="max-w-[200px] truncate text-sm text-slate-500 font-medium bg-slate-50/50 px-4 py-2 rounded-xl group-hover:bg-white/50 transition-colors" title="{{ $order->fault }}">
                                        {{ $order->fault ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center bg-white group-hover:bg-transparent transition-colors">
                                    <x-enhanced-status-badge :status="$order->status->value ?? $order->status" />
                                </td>
                                <td class="px-6 py-5 bg-white group-hover:bg-transparent transition-colors">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">
                                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                                {{ \Morilog\Jalali\Jalalian::fromCarbon($order->created_at)->format('Y/m/d') }}
                                            @else
                                                {{ $order->created_at->format('Y/m/d') }}
                                            @endif
                                        </span>
                                        <span class="text-[10px] font-medium text-slate-400">
                                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                                {{ \Morilog\Jalali\Jalalian::fromCarbon($order->created_at)->format('H:i') }}
                                            @else
                                                {{ $order->created_at->format('H:i') }}
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center bg-white group-hover:bg-transparent transition-colors">
                                    @if($order->total_cost > 0)
                                        <div class="flex flex-col items-center">
                                            <span class="text-base font-black text-primary-600">{{ number_format($order->total_cost) }}</span>
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">تومان</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-400 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-100 group-hover:bg-white transition-colors">
                                            <i class="ti ti-hourglass-low animate-pulse text-sm"></i>
                                            در انتظار برآورد
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center bg-white group-hover:bg-transparent transition-colors last:rounded-l-[2rem]">
                                    <div class="flex items-center justify-center">
                                        <a href="{{ route('customer.orders.show', $order) }}" class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center hover:bg-blue-600 hover:text-white hover:scale-110 hover:rotate-12 transition-all duration-500 shadow-sm group-hover:shadow-md" title="مشاهده جزئیات">
                                            <i class="ti ti-eye text-xl"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-20 text-center">
                                    <div class="flex flex-col items-center gap-8 max-w-md mx-auto">
                                        <div class="text-slate-400 font-medium">سفارشی یافت نشد.</div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($repairOrders->hasPages())
                <div class="mt-8 pt-8 border-t border-slate-50">
                    {{ $repairOrders->links() }}
                </div>
                @endif
            </x-enhanced-card>
        </div>

        <!-- Shop Orders Section -->
        <div x-show="activeTab === 'shop'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <x-enhanced-card icon="shopping-cart">
                <x-slot name="title">
                    <div class="flex items-center gap-4">
                        <span class="text-xl font-black text-slate-900">سفارشات فروشگاه</span>
                        <div class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                            {{ $shopOrders->total() }} سفارش
                        </div>
                    </div>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-separate border-spacing-y-4">
                        <thead>
                            <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                                <th class="px-6 py-2">شماره سفارش</th>
                                <th class="px-6 py-2">اقلام</th>
                                <th class="px-6 py-2 text-center">وضعیت سفارش</th>
                                <th class="px-6 py-2 text-center">وضعیت پرداخت</th>
                                <th class="px-6 py-2">تاریخ ثبت</th>
                                <th class="px-6 py-2 text-center">مبلغ کل</th>
                                <th class="px-6 py-2 text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shopOrders as $order)
                            <tr class="group hover:bg-slate-50/80 transition-all duration-500">
                                <td class="px-6 py-5 first:rounded-r-[2rem] bg-white group-hover:bg-transparent transition-colors">
                                    <span class="font-black text-slate-900 bg-slate-100 px-4 py-2 rounded-xl group-hover:bg-white transition-colors"><x-hash-ref :value="$order->order_number" /></span>
                                </td>
                                <td class="px-6 py-5 bg-white group-hover:bg-transparent transition-colors">
                                    <div class="flex flex-col gap-1">
                                        @foreach($order->items->take(2) as $item)
                                            <span class="text-xs font-bold text-slate-700">{{ $item->product->name }} ({{ $item->quantity }} عدد)</span>
                                        @endforeach
                                        @if($order->items->count() > 2)
                                            <span class="text-[10px] text-slate-400">+ {{ $order->items->count() - 2 }} مورد دیگر</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center bg-white group-hover:bg-transparent transition-colors">
                                    <x-enhanced-status-badge :status="$order->status->value ?? $order->status" />
                                </td>
                                <td class="px-6 py-5 text-center bg-white group-hover:bg-transparent transition-colors">
                                    @php
                                        $paymentVariant = match($order->payment_status->value ?? $order->payment_status) {
                                            'paid'     => 'success-solid',
                                            'failed'   => 'danger',
                                            'refunded' => 'secondary',
                                            default    => 'warning',
                                        };
                                    @endphp
                                    <x-enhanced-status-badge
                                        :status="$order->payment_status->value ?? $order->payment_status"
                                        :label="$order->payment_status->label()"
                                        :variant="$paymentVariant"
                                    />
                                </td>
                                <td class="px-6 py-5 bg-white group-hover:bg-transparent transition-colors">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">
                                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                                {{ \Morilog\Jalali\Jalalian::fromCarbon($order->created_at)->format('Y/m/d') }}
                                            @else
                                                {{ $order->created_at->format('Y/m/d') }}
                                            @endif
                                        </span>
                                        <span class="text-[10px] font-medium text-slate-400">
                                            @if(class_exists('\Morilog\Jalali\Jalalian'))
                                                {{ \Morilog\Jalali\Jalalian::fromCarbon($order->created_at)->format('H:i') }}
                                            @else
                                                {{ $order->created_at->format('H:i') }}
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center bg-white group-hover:bg-transparent transition-colors">
                                    <div class="flex flex-col items-center">
                                        <span class="text-base font-black text-emerald-600">{{ number_format($order->total) }}</span>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">تومان</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center bg-white group-hover:bg-transparent transition-colors last:rounded-l-[2rem]">
                                    <div class="flex items-center justify-center">
                                        <a href="{{ route('customer.orders.shop-show', $order) }}" class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center hover:bg-emerald-600 hover:text-white hover:scale-110 hover:rotate-12 transition-all duration-500 shadow-sm group-hover:shadow-md" title="مشاهده جزئیات">
                                            <i class="ti ti-eye text-xl"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-20 text-center">
                                    <div class="flex flex-col items-center gap-8 max-w-md mx-auto">
                                        <div class="text-slate-400 font-medium">سفارش فروشی یافت نشد.</div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($shopOrders->hasPages())
                <div class="mt-8 pt-8 border-t border-slate-50">
                    {{ $shopOrders->links() }}
                </div>
                @endif
            </x-enhanced-card>
        </div>
    </div>
</div>
@endsection
