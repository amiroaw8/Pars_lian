@extends('layouts.app')

@section('title', 'جزئیات سفارش ' . hash_ref_plain($order->order_number) . ' - پارس لیان')

@section('content')
<x-page-header 
    subtitle="مشاهده کامل اطلاعات سفارش، اقلام خریداری شده و مدیریت وضعیت ارسال و پرداخت."
    badge="مدیریت سفارش"
    badgeIcon="ti-receipt-2"
    headerIcon="ti-shopping-cart"
    actionUrl="{{ route('automation.orders.index') }}"
    actionText="بازگشت به لیست"
    class="mb-8"
>
    <x-slot:title>
        جزئیات سفارش <x-hash-ref :value="$order->order_number" />
    </x-slot:title>
</x-page-header>

<div class="flex justify-end gap-3 mb-8 px-4 animate-fade-in flex-wrap">
    <button onclick="printOrder()" class="btn-modern btn-modern-secondary flex items-center gap-2 px-6 py-3">
        <i class="ti ti-printer text-xl"></i>
        <span class="font-bold">چاپ صفحه</span>
    </button>
    <div class="relative inline-block">
        <button type="button" onclick="toggleOrderInvoiceMenu()" class="btn-modern btn-modern-primary flex items-center gap-2 px-6 py-3 shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transition-all duration-300 group">
            <i class="ti ti-file-invoice text-xl group-hover:scale-110 transition-transform duration-300"></i>
            <span class="font-bold">چاپ فاکتور</span>
            <i class="ti ti-chevron-down text-sm"></i>
        </button>
        <div id="order-invoice-menu" class="hidden absolute left-0 top-full mt-2 z-50 min-w-[220px] bg-white rounded-2xl shadow-xl border border-slate-100 py-2">
            <a href="{{ route('automation.orders.print', ['order' => $order, 'type' => 'invoice']) }}" target="_blank" class="block w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">فاکتور فروش</a>
            <a href="{{ route('automation.orders.print', ['order' => $order, 'type' => 'proforma']) }}" target="_blank" class="block w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">پیش‌فاکتور</a>
            <a href="{{ route('automation.orders.print', ['order' => $order, 'type' => 'receipt']) }}" target="_blank" class="block w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">رسید فروش</a>
            <a href="{{ route('automation.orders.print', ['order' => $order, 'type' => 'delivery']) }}" target="_blank" class="block w-full text-right px-4 py-2.5 text-sm hover:bg-slate-50">رسید تحویل کالا</a>
        </div>
    </div>
    @if($order->canBeCancelled())
        <button type="button" onclick="openCancelOrderModal()" class="btn-modern btn-modern-danger flex items-center gap-2 px-6 py-3">
            <i class="ti ti-ban text-xl"></i>
            <span class="font-bold">لغو سفارش</span>
        </button>
    @endif
</div>

@if($order->canBeCancelled())
<div id="cancelOrderModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-[2rem] shadow-2xl max-w-lg w-full p-8 border border-slate-100">
        <h3 class="text-xl font-black text-slate-900 mb-2 flex items-center gap-2">
            <i class="ti ti-alert-triangle text-rose-500"></i>
            لغو سفارش
        </h3>
        <p class="text-sm text-slate-500 mb-6">موجودی اقلام سفارش در صورت لغو، به انبار بازگردانده می‌شود.</p>
        <form action="{{ route('automation.orders.cancel', $order) }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="cancel_reason" class="form-label">یادداشت لغو (اختیاری)</label>
                <textarea name="cancel_reason" id="cancel_reason" rows="3" class="form-control" placeholder="دلیل لغو سفارش برای تیم داخلی...">{{ old('cancel_reason') }}</textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeCancelOrderModal()" class="btn-modern btn-modern-light px-6">انصراف</button>
                <button type="submit" class="btn-modern btn-modern-danger px-6">تأیید لغو سفارش</button>
            </div>
        </form>
    </div>
</div>
@endif

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

        <!-- اطلاعات رهگیری پستی (New Section) -->
        <x-enhanced-card title="رهگیری مرسوله و وضعیت ارسال" icon="ti ti-package" animated>
            <form action="{{ route('automation.orders.update-tracking', $order) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                @csrf
                <div class="form-group">
                    <label for="tracking_code" class="form-label">کد رهگیری پستی (تپیاکس/پست/...)</label>
                    <input type="text" name="tracking_code" id="tracking_code" value="{{ $order->tracking_code }}" class="form-control" placeholder="مثلاً: 123456789012">
                </div>
                <div class="form-group">
                    <label for="shipping_status" class="form-label">وضعیت جزیی ارسال</label>
                    <input type="text" name="shipping_status" id="shipping_status" value="{{ $order->shipping_status }}" class="form-control" placeholder="مثلاً: تحویل به تیپاکس">
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="btn-modern btn-modern-success h-11 px-8">
                        <i class="ti ti-device-floppy"></i>
                        بروزرسانی اطلاعات رهگیری
                    </button>
                </div>
            </form>
        </x-enhanced-card>
    </div>

    <!-- ستون سمت چپ: مدیریت وضعیت و اطلاعات تکمیلی -->
    <div class="space-y-8">
        <!-- مدیریت وضعیت -->
        <x-enhanced-card title="مدیریت وضعیت" icon="ti ti-bolt" animated>
            <form action="{{ route('automation.orders.update-status', $order) }}" method="POST" class="space-y-6">
                @csrf
                <div class="form-group">
                    <label for="status" class="form-label">وضعیت سفارش</label>
                    <select name="status" id="status" class="form-control focus:ring-2 focus:ring-primary-500/20">
                        @foreach(\App\Enums\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ $order->status->value == $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_status" class="form-label">وضعیت پرداخت</label>
                    <select name="payment_status" id="payment_status" class="form-control focus:ring-2 focus:ring-emerald-500/20">
                        @foreach(\App\Enums\PaymentStatus::cases() as $pStatus)
                            <option value="{{ $pStatus->value }}" {{ $order->payment_status->value == $pStatus->value ? 'selected' : '' }}>
                                {{ $pStatus->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn-modern btn-modern-primary w-full justify-center py-4 shadow-xl shadow-primary-500/20 group">
                    <i class="ti ti-check text-xl group-hover:scale-125 transition-transform"></i>
                    <span>بروزرسانی وضعیت</span>
                </button>
            </form>
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
                <div class="flex justify-between items-center py-3 border-b border-slate-50">
                    <span class="text-sm text-slate-500 font-bold">وضعیت نهایی:</span>
                    <x-enhanced-status-badge :status="$order->status->value" />
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

        @if($order->service_order_id)
        <x-enhanced-card title="سفارش سرویس مرتبط" icon="ti ti-clipboard-list" animated>
            <div class="p-4 rounded-2xl bg-primary-50 border border-primary-100 mb-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-primary-600 shadow-sm">
                        <i class="ti ti-tool text-xl"></i>
                    </div>
                    <div>
                        <div class="text-xs font-black text-primary-900">سفارش سرویس <x-hash-ref :value="$order->service_order_id" /></div>
                        <div class="text-[10px] text-primary-600 font-bold">{{ $order->serviceOrder->status->label() }}</div>
                    </div>
                </div>
                <a href="{{ route('automation.service-orders.show', $order->service_order_id) }}" class="btn-modern btn-modern-primary w-full justify-center py-2 text-xs">
                    مشاهده جزئیات سرویس
                </a>
            </div>
        </x-enhanced-card>
        @endif

        <x-enhanced-card title="یادداشت‌های سفارش" icon="ti ti-notes" animated>
            <div class="space-y-6">
                <form action="{{ route('automation.orders.notes.store', $order) }}" method="POST" class="space-y-4 p-5 rounded-2xl bg-slate-50 border border-slate-100">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">متن یادداشت</label>
                        <textarea name="body" rows="3" class="form-control" required placeholder="یادداشت جدید..."></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-200 bg-white cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="visibility" value="internal" class="text-blue-600" checked>
                            <span>
                                <span class="block text-sm font-black text-slate-800">یادداشت داخلی</span>
                                <span class="block text-xs text-slate-500">فقط تیم اتوماسیون</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-200 bg-white cursor-pointer has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                            <input type="radio" name="visibility" value="customer" class="text-emerald-600">
                            <span>
                                <span class="block text-sm font-black text-slate-800">یادداشت برای مشتری</span>
                                <span class="block text-xs text-slate-500">قابل مشاهده در پنل مشتری</span>
                            </span>
                        </label>
                    </div>
                    <button type="submit" class="btn-modern btn-modern-primary w-full justify-center">
                        <i class="ti ti-plus"></i>
                        ثبت یادداشت
                    </button>
                </form>

                @if($order->orderNotes->isNotEmpty())
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        @foreach($order->orderNotes as $note)
                            <div class="p-4 rounded-2xl border {{ $note->isInternal() ? 'bg-slate-50 border-slate-100' : 'bg-emerald-50/50 border-emerald-100' }}">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider {{ $note->isInternal() ? 'text-slate-500' : 'text-emerald-700' }}">
                                        {{ $note->isInternal() ? 'داخلی' : 'مشتری' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-bold">
                                        {{ $note->user?->name ?? 'سیستم' }}
                                        ·
                                        @if(class_exists('\Morilog\Jalali\Jalalian'))
                                            {{ \Morilog\Jalali\Jalalian::fromCarbon($note->created_at)->format('Y/m/d H:i') }}
                                        @else
                                            {{ $note->created_at->format('Y/m/d H:i') }}
                                        @endif
                                    </span>
                                </div>
                                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $note->body }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-400 text-center py-4">هنوز یادداشتی ثبت نشده است.</p>
                @endif
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

    function toggleOrderInvoiceMenu() {
        document.getElementById('order-invoice-menu')?.classList.toggle('hidden');
    }

    function openCancelOrderModal() {
        document.getElementById('cancelOrderModal')?.classList.remove('hidden');
    }

    function closeCancelOrderModal() {
        document.getElementById('cancelOrderModal')?.classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('order-invoice-menu');
        if (!menu || menu.classList.contains('hidden')) return;
        if (!e.target.closest('#order-invoice-menu') && !e.target.closest('[onclick*="toggleOrderInvoiceMenu"]')) {
            menu.classList.add('hidden');
        }
    });
</script>
@endpush
