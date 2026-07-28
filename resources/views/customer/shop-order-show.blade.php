@extends('layouts.app')

@section('title', 'Buy - ' . $order->order_number)

@section('content')
<x-page-header 
    subtitle="مشاهده اطلاعات کامل سفارش، اقلام خریداری شده و وضعیت ارسال."
    badge="سفارش فروشگاه"
    badgeIcon="ti-receipt-2"
    headerIcon="ti-shopping-cart"
    actionUrl="{{ route('customer.orders') }}"
    actionText="بازگشت به سفارشات من"
    class="mb-8"
>
    <x-slot:title>
        جزئیات سفارش <x-hash-ref :value="$order->order_number" />
    </x-slot:title>
</x-page-header>

<div class="flex justify-end mb-8 px-4 animate-fade-in gap-4">
    @if(($order->status === \App\Enums\OrderStatus::PENDING || $order->payment_status === \App\Enums\PaymentStatus::PENDING || $order->payment_status === \App\Enums\PaymentStatus::FAILED) && $order->payment_method === 'online')
    <a href="{{ route('payment.pay', $order) }}" class="btn-modern btn-modern-primary flex items-center gap-2 px-6 py-3">
        <i class="ti ti-credit-card"></i>
        <span>پرداخت آنلاین</span>
    </a>
    @endif

    <button onclick="printOrder()" class="btn-modern btn-modern-secondary flex items-center gap-2 px-6 py-3">
        <i class="ti ti-printer"></i>
        <span>چاپ فاکتور</span>
    </button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-slide-up">
    <!-- ستون سمت راست: اطلاعات اصلی و اقلام -->
    <div class="lg:col-span-2 space-y-8">
        <!-- اقلام سفارش -->
        <x-enhanced-card title="اقلام سفارش" icon="ti ti-list-details" animated>
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">محصول</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider text-center">قیمت واحد</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider text-center">تعداد</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider text-center">جمع کل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($order->items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 overflow-hidden border border-slate-100">
                                        @if($item->product->main_image_url ?? false)
                                            <img loading="lazy" src="{{ $item->product->main_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="ti ti-photo text-2xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $item->product->name }}</span>
                                        <span class="text-xs text-slate-500">شناسه: <x-hash-ref :value="$item->product->id" /></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center font-bold text-slate-700">
                                {{ number_format($item->price) }} <span class="text-[10px] text-slate-400">تومان</span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 font-black text-slate-700">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center font-black text-primary-600">
                                {{ number_format($item->total) }} <span class="text-[10px]">تومان</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-8 p-8 rounded-[2.5rem] bg-slate-50 border border-slate-100">
                <div class="space-y-4 max-w-sm mr-auto">
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-bold text-sm">جمع کل اقلام:</span>
                        <span class="font-black">{{ number_format($order->subtotal) }} تومان</span>
                    </div>
                    @if($order->shipping_amount > 0)
                    <div class="flex justify-between items-center text-slate-600">
                        <span class="font-bold text-sm">هزینه ارسال:</span>
                        <span class="font-black">{{ number_format($order->shipping_amount) }} تومان</span>
                    </div>
                    @endif
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between items-center text-rose-600">
                        <span class="font-bold text-sm">تخفیف:</span>
                        <span class="font-black">-{{ number_format($order->discount_amount) }} تومان</span>
                    </div>
                    @endif
                    <div class="pt-4 border-t border-slate-200 flex justify-between items-center text-slate-900">
                        <span class="font-black text-lg">مبلغ نهایی پرداخت:</span>
                        <span class="font-black text-2xl text-primary-600">{{ number_format($order->total) }} تومان</span>
                    </div>
                </div>
            </div>
        </x-enhanced-card>

        <!-- اطلاعات ارسال -->
        <x-enhanced-card title="اطلاعات تحویل و گیرنده" icon="ti ti-truck-delivery" animated>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
                        <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-blue-500 shadow-sm border border-slate-100">
                            <i class="ti ti-user text-xl"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 font-bold mb-1">نام و نام خانوادگی گیرنده</div>
                            <div class="font-black text-slate-800 text-lg">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
                        <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-emerald-500 shadow-sm border border-slate-100">
                            <i class="ti ti-phone text-xl"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 font-bold mb-1">شماره تماس</div>
                            <div class="font-black text-slate-800 text-lg">{{ $order->shipping_phone }}</div>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
                        <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-amber-500 shadow-sm border border-slate-100">
                            <i class="ti ti-map-pin text-xl"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 font-bold mb-1">آدرس تحویل</div>
                            <div class="font-bold text-slate-800 leading-relaxed">
                                {{ $order->shipping_state }}، {{ $order->shipping_city }}<br>
                                {{ $order->shipping_address }}<br>
                                <span class="text-xs text-slate-500">کد پستی: {{ $order->shipping_postal_code }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
                        <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-indigo-500 shadow-sm border border-slate-100">
                            <i class="ti ti-truck-delivery text-xl"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-400 font-bold mb-1">روش ارسال</div>
                            <div class="font-black text-slate-800 text-lg">{{ $order->shipping_method_label }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @if($order->notes)
            <div class="mt-6 p-6 rounded-2xl bg-amber-50 border border-amber-100 text-amber-800">
                <div class="flex items-center gap-2 font-black mb-2 text-xs">
                    <i class="ti ti-note"></i>
                    یادداشت سفارش:
                </div>
                <p class="text-sm font-medium leading-relaxed">{{ $order->notes }}</p>
            </div>
            @endif
        </x-enhanced-card>
    </div>

    <!-- ستون سمت چپ: وضعیت و اطلاعات تکمیلی -->
    <div class="space-y-8">
        <!-- وضعیت فعلی -->
        <x-enhanced-card title="وضعیت سفارش" icon="ti ti-bolt" animated>
            <div class="space-y-6">
                <div class="p-6 rounded-[2rem] bg-slate-50 border border-slate-100 text-center">
                    <div class="text-xs text-slate-400 font-black uppercase tracking-widest mb-4">وضعیت فعلی</div>
                    <x-enhanced-status-badge :status="$order->status->value" size="lg" />
                    <p class="mt-6 text-sm text-slate-500 font-medium leading-relaxed">
                        @switch($order->status->value)
                            @case('pending') سفارش شما دریافت شده و در انتظار بررسی توسط تیم ما است. @break
                            @case('processing') سفارش شما تأیید شده و در حال آماده‌سازی برای ارسال است. @break
                            @case('shipped') سفارش شما ارسال شده و در راه است. @break
                            @case('delivered') سفارش شما با موفقیت تحویل داده شده است. @break
                            @case('cancelled') این سفارش لغو شده است. @break
                            @default سفارش شما در حال پردازش می‌باشد.
                        @endswitch
                    </p>
                </div>

                <div class="p-6 rounded-[2rem] bg-emerald-50 border border-emerald-100 text-center">
                    <div class="text-xs text-emerald-400 font-black uppercase tracking-widest mb-4">وضعیت پرداخت</div>
                    @php
                        $paymentVariant = match($order->payment_status->value) {
                            'paid'     => 'success-solid',
                            'failed'   => 'danger',
                            'refunded' => 'secondary',
                            default    => 'warning',
                        };
                    @endphp
                    <x-enhanced-status-badge
                        :status="$order->payment_status->value"
                        :label="$order->payment_status->label()"
                        :variant="$paymentVariant"
                        size="lg"
                    />
                </div>
            </div>
        </x-enhanced-card>

        <!-- اطلاعات مالی و پرداخت -->
        <x-enhanced-card title="اطلاعات پرداخت" icon="ti ti-wallet" animated>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-slate-50">
                    <span class="text-sm text-slate-500 font-bold">روش پرداخت:</span>
                    <span class="font-black text-slate-800">
                        {{ $order->payment_method == 'cod' ? 'پرداخت در محل' : 'آنلاین (بانکی)' }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm text-slate-500 font-bold">تاریخ ثبت:</span>
                    <span class="font-black text-slate-800 dir-ltr">
                        @if(class_exists('\Morilog\Jalali\Jalalian'))
                            {{ \Morilog\Jalali\Jalalian::fromCarbon($order->created_at)->format('Y/m/d H:i') }}
                        @else
                            {{ $order->created_at->format('Y/m/d H:i') }}
                        @endif
                    </span>
                </div>
            </div>
        </x-enhanced-card>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printOrder() {
        const originalTitle = document.title;
        document.title = "Buy - {{ $order->order_number }}";
        window.print();
        setTimeout(() => { document.title = originalTitle; }, 100);
    }
</script>
@endpush
