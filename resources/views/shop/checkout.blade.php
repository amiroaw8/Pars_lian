@extends('layouts.shop')

@section('title', 'تسویه حساب - پارس لیان')

@section('shop-content')
<div class="py-12 bg-gray-50 min-h-screen" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-sm font-medium" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-reverse space-x-2">
                <li><a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-blue-600 transition-colors">فروشگاه</a></li>
                <li>
                    <i class="ti ti-chevron-left text-gray-400 mx-2"></i>
                    <a href="{{ route('cart.index') }}" class="text-gray-500 hover:text-blue-600 transition-colors">سبد خرید</a>
                </li>
                <li class="flex items-center">
                    <i class="ti ti-chevron-left text-gray-400 mx-2"></i>
                    <span class="text-gray-900">تسویه حساب</span>
                </li>
            </ol>
        </nav>

        <h1 class="text-4xl font-black text-gray-900 mb-10 tracking-tight">تسویه حساب</h1>

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Shipping Details -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                        <h2 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-sm">۱</span>
                            اطلاعات ارسال
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">نام</label>
                                <input type="text" name="shipping_first_name" value="{{ old('shipping_first_name', auth()->user()->first_name ?? '') }}" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all" required>
                                @error('shipping_first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">نام خانوادگی</label>
                                <input type="text" name="shipping_last_name" value="{{ old('shipping_last_name', auth()->user()->last_name ?? '') }}" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all" required>
                                @error('shipping_last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">ایمیل (اختیاری)</label>
                                <input type="email" name="shipping_email" value="{{ old('shipping_email', auth()->user()->email ?? '') }}" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all">
                                @error('shipping_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">شماره تماس</label>
                                <input type="text" name="shipping_phone" value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all" required>
                                @error('shipping_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">آدرس دقیق (خیابان، کوچه، پلاک)</label>
                                <textarea name="shipping_address" rows="3" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all" required placeholder="مثلاً: خیابان اصلی، کوچه فرعی، پلاک ۱۰">{{ old('shipping_address', (auth()->check() && (auth()->user()->street || auth()->user()->alley || auth()->user()->plate)) ? trim((auth()->user()->street ? auth()->user()->street . '، ' : '') . (auth()->user()->alley ? auth()->user()->alley . '، ' : '') . (auth()->user()->plate ? 'پلاک ' . auth()->user()->plate : '')) : '') }}</textarea>
                                @error('shipping_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">استان</label>
                                <input type="text" name="shipping_state" value="{{ old('shipping_state', auth()->user()->province ?? '') }}" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all" required placeholder="مثلاً: تهران">
                                @error('shipping_state') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">شهر</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city', auth()->user()->city ?? '') }}" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all" required placeholder="مثلاً: تهران">
                                @error('shipping_city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label id="postalCodeLabel" class="block text-sm font-bold text-gray-700 mb-2">
                                    کد پستی
                                    <span id="postalRequiredMark" class="text-red-500 hidden">*</span>
                                    <span id="postalOptionalNote" class="text-gray-400 font-medium">(اختیاری)</span>
                                </label>
                                <input type="text" name="shipping_postal_code" id="shipping_postal_code" value="{{ old('shipping_postal_code', auth()->user()->postal_code ?? '') }}" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all" placeholder="کد پستی ۱۰ رقمی">
                                @error('shipping_postal_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                        <h2 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-sm">۲</span>
                            روش ارسال
                        </h2>

                        <div class="grid grid-cols-1 gap-4">
                            <label class="checkout-choice group relative flex items-center p-6 border-2 rounded-[1.5rem] cursor-pointer transition-all hover:bg-gray-50 border-gray-100">
                                <input type="radio" name="shipping_method" value="tipax" class="sr-only shipping-method-radio" {{ old('shipping_method') === 'tipax' ? 'checked' : '' }}>
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center ml-4 group-has-[:checked]:scale-110 transition-transform shrink-0">
                                    <i class="ti ti-truck-delivery text-2xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1 gap-2">
                                        <span class="font-black text-gray-900">تیپاکس (Tipax)</span>
                                        <span class="text-xs font-bold px-3 py-1 bg-amber-50 text-amber-600 rounded-full shrink-0">پس‌کرایه</span>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed">ارسال به سراسر کشور | زمان تحویل: ۲ تا ۴ روز کاری | هزینه ارسال بر عهده مشتری هنگام تحویل</p>
                                </div>
                                <div class="checkout-choice-check absolute top-4 left-4 text-blue-600 transition-opacity">
                                    <i class="ti ti-circle-check-filled text-xl"></i>
                                </div>
                            </label>

                            <label class="checkout-choice group relative flex items-center p-6 border-2 rounded-[1.5rem] cursor-pointer transition-all hover:bg-gray-50 border-gray-100">
                                <input type="radio" name="shipping_method" value="dekapost" class="sr-only shipping-method-radio" {{ old('shipping_method') === 'dekapost' ? 'checked' : '' }}>
                                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center ml-4 group-has-[:checked]:scale-110 transition-transform shrink-0">
                                    <i class="ti ti-package text-2xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1 gap-2">
                                        <span class="font-black text-gray-900">دکا پست (DekaPost)</span>
                                        <span class="text-xs font-bold px-3 py-1 bg-amber-50 text-amber-600 rounded-full shrink-0">پس‌کرایه</span>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed">ارسال به سراسر کشور | زمان تحویل: ۲ تا ۴ روز کاری | هزینه ارسال بر عهده مشتری هنگام تحویل</p>
                                </div>
                                <div class="checkout-choice-check absolute top-4 left-4 text-blue-600 transition-opacity">
                                    <i class="ti ti-circle-check-filled text-xl"></i>
                                </div>
                            </label>

                            <label class="checkout-choice group relative flex items-center p-6 border-2 rounded-[1.5rem] cursor-pointer transition-all hover:bg-gray-50 border-gray-100">
                                <input type="radio" name="shipping_method" value="snapp" class="sr-only shipping-method-radio" {{ old('shipping_method') === 'snapp' ? 'checked' : '' }}>
                                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center ml-4 group-has-[:checked]:scale-110 transition-transform shrink-0">
                                    <i class="ti ti-moped text-2xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1 gap-2">
                                        <span class="font-black text-gray-900">اسنپ / پیک (Snapp)</span>
                                        <span class="text-xs font-bold px-3 py-1 bg-amber-50 text-amber-600 rounded-full shrink-0">پس‌کرایه</span>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed">مخصوص خرم‌آباد و شهرهای اطراف | تحویل همان روز (سفارش‌های قبل از ساعت کاری) | هزینه پیک در محل</p>
                                </div>
                                <div class="checkout-choice-check absolute top-4 left-4 text-blue-600 transition-opacity">
                                    <i class="ti ti-circle-check-filled text-xl"></i>
                                </div>
                            </label>

                            <label class="checkout-choice group relative flex items-center p-6 border-2 rounded-[1.5rem] cursor-pointer transition-all hover:bg-gray-50 border-gray-100">
                                <input type="radio" name="shipping_method" value="pickup" class="sr-only shipping-method-radio" {{ old('shipping_method') === 'pickup' ? 'checked' : '' }}>
                                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center ml-4 group-has-[:checked]:scale-110 transition-transform shrink-0">
                                    <i class="ti ti-building-store text-2xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1 gap-2">
                                        <span class="font-black text-gray-900">تحویل حضوری</span>
                                        <span class="text-xs font-bold px-3 py-1 bg-green-50 text-green-600 rounded-full shrink-0">رایگان</span>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed">دریافت در دفتر شرکت | شنبه تا پنج‌شنبه ۸:۳۰ تا ۱۲:۳۰ (بجز تعطیلات) | آدرس: دفتر مرکزی پارس لیان</p>
                                </div>
                                <div class="checkout-choice-check absolute top-4 left-4 text-blue-600 transition-opacity">
                                    <i class="ti ti-circle-check-filled text-xl"></i>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                        <h2 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-sm">۳</span>
                            روش پرداخت
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <label class="checkout-choice group relative flex flex-col p-6 border-2 rounded-[1.5rem] cursor-pointer transition-all hover:bg-gray-50 border-gray-100">
                                <input type="radio" name="payment_method" value="online" class="sr-only" {{ old('payment_method', 'online') !== 'cod' ? 'checked' : '' }}>
                                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4 group-has-[:checked]:bg-blue-600 group-has-[:checked]:text-white transition-colors">
                                    <i class="ti ti-credit-card text-2xl"></i>
                                </div>
                                <span class="font-black text-gray-900">پرداخت آنلاین</span>
                                <span class="text-xs text-gray-400 mt-1">درگاه پرداخت شتاب</span>
                                <div class="checkout-choice-check absolute top-4 left-4 text-blue-600 transition-opacity">
                                    <i class="ti ti-circle-check-filled text-xl"></i>
                                </div>
                            </label>

                            <label id="paymentCodLabel" class="checkout-choice group relative flex flex-col p-6 border-2 rounded-[1.5rem] cursor-pointer transition-all hover:bg-gray-50 border-gray-100">
                                <input type="radio" name="payment_method" id="paymentCodRadio" value="cod" class="sr-only" {{ old('payment_method') === 'cod' ? 'checked' : '' }}>
                                <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center mb-4 group-has-[:checked]:bg-green-600 group-has-[:checked]:text-white transition-colors">
                                    <i class="ti ti-cash text-2xl"></i>
                                </div>
                                <span class="font-black text-gray-900">پرداخت در محل</span>
                                <span class="text-xs text-gray-400 mt-1">فقط برای تحویل حضوری</span>
                                <div class="checkout-choice-check absolute top-4 left-4 text-blue-600 transition-opacity">
                                    <i class="ti ti-circle-check-filled text-xl"></i>
                                </div>
                            </label>
                        </div>

                        <!-- Sub-options for active online payment gateways -->
                        <div id="onlineGatewayContainer" class="pt-6 border-t border-gray-100">
                            <p class="text-xs font-bold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="ti ti-building-bank text-blue-600 text-base"></i>
                                انتخاب درگاه پرداخت آنلاین:
                            </p>
                            <div class="flex flex-col gap-3">
                                @foreach($activeGateways ?? [] as $gatewayKey => $gateway)
                                    @php
                                        $isChecked = (old('payment_gateway') === $gatewayKey) || (!old('payment_gateway') && !empty($gateway['is_default']));
                                    @endphp
                                    <label class="gateway-option-label flex items-center gap-4 p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200 bg-white {{ $isChecked ? 'border-blue-600 bg-blue-50/60 shadow-md shadow-blue-100' : 'border-gray-200 hover:border-blue-300' }}">
                                        <input type="radio" name="payment_gateway" value="{{ $gatewayKey }}" class="gateway-radio sr-only" {{ $isChecked ? 'checked' : '' }}>

                                        <!-- Icon -->
                                        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-colors {{ $isChecked ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }} gateway-icon">
                                            <i class="{{ $gateway['icon'] ?? 'ti ti-credit-card' }} text-xl"></i>
                                        </div>

                                        <!-- Name & description -->
                                        <div class="flex-1 min-w-0">
                                            <span class="block text-sm font-black text-gray-900 leading-tight">{{ $gateway['name'] }}</span>
                                            <span class="block text-[11px] text-gray-500 mt-0.5 leading-snug">{{ $gateway['description'] ?? 'پرداخت آنلاین' }}</span>
                                        </div>

                                        <!-- Check indicator -->
                                        <div class="gateway-check-icon shrink-0 transition-opacity {{ $isChecked ? 'opacity-100 text-blue-600' : 'opacity-0 text-blue-600' }}">
                                            <i class="ti ti-circle-check-filled text-2xl"></i>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 sm:p-10">
                        <label class="block text-sm font-bold text-gray-700 mb-4">یادداشت سفارش (اختیاری)</label>
                        <textarea name="notes" rows="3" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all" placeholder="توضیحات تکمیلی در مورد سفارش..."></textarea>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-blue-900/5 border border-gray-100 p-8 sticky top-28">
                        <h2 class="text-2xl font-black text-gray-900 mb-8">خلاصه سفارش</h2>
                        
                        <div class="space-y-4 mb-8 max-h-60 overflow-y-auto custom-scrollbar pr-2">
                            @foreach($cart->items as $item)
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-50 rounded-lg overflow-hidden flex-shrink-0">
                                        <img loading="lazy" src="{{ $item->product->main_image_url }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs font-bold text-gray-900 truncate">{{ $item->product->name }}</h4>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $item->quantity }} عدد</p>
                                    </div>
                                    <div class="text-xs font-black text-gray-900">{{ number_format($item->subtotal) }} تومان</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-6 mb-8 pt-8 border-t border-gray-100">
                            <div class="flex justify-between text-gray-600">
                                <span>جمع سفارش</span>
                                <span class="font-bold text-gray-900">{{ number_format($cart->subtotal) }} تومان</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>هزینه ارسال</span>
                                <span id="checkout-shipping-label" class="text-amber-600 font-bold">نوع ارسال هنوز مشخص نشده</span>
                            </div>
                            <div class="pt-6 border-t border-gray-100 flex justify-between items-end">
                                <span class="text-gray-900 font-bold">مبلغ قابل پرداخت</span>
                                <div class="text-right">
                                    <div class="text-3xl font-black text-blue-600">{{ number_format($cart->total) }}</div>
                                    <div class="text-xs text-gray-400 mt-1">تومان</div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="submit-order-btn" class="w-full py-5 bg-blue-600 text-white rounded-2xl font-black text-lg flex items-center justify-center gap-3 hover:bg-blue-700 hover:scale-[1.02] transition-all shadow-2xl shadow-blue-200">
                            <span id="btn-text">ثبت نهایی سفارش</span>
                            <span id="btn-loading" class="hidden">
                                <i class="ti ti-loader animate-spin text-xl"></i>
                                در حال پردازش...
                            </span>
                            <i id="btn-icon" class="ti ti-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function syncCheckoutChoices() {
        document.querySelectorAll('.checkout-choice').forEach(function(label) {
            const input = label.querySelector('input[type="radio"]');
            if (!input) return;
            label.classList.toggle('is-selected', input.checked);
            label.classList.toggle('is-disabled', input.disabled);
        });
    }

    document.querySelectorAll('.checkout-choice input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', syncCheckoutChoices);
    });
</script>

<style>
    .checkout-choice { border-color: #e5e7eb; transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease; }
    .checkout-choice.is-selected,
    .checkout-choice:has(input[type="radio"]:checked) {
        border-color: #2563eb !important;
        background: rgba(239, 246, 255, 0.85) !important;
        box-shadow: 0 10px 25px -8px rgba(37, 99, 235, 0.22) !important;
    }
    .checkout-choice .checkout-choice-check { opacity: 0; }
    .checkout-choice.is-selected .checkout-choice-check,
    .checkout-choice:has(input[type="radio"]:checked) .checkout-choice-check { opacity: 1 !important; }
    .checkout-choice.is-disabled { opacity: 0.45; pointer-events: none; filter: grayscale(0.2); }
</style>

<script>
    const shippingLabels = {
        tipax: 'پس‌کرایه (تیپاکس)',
        dekapost: 'پس‌کرایه (دکا پست)',
        snapp: 'پس‌کرایه (پیک)',
        pickup: 'رایگان (تحویل حضوری)',
    };

    function updateCheckoutShippingLabel() {
        const selected = document.querySelector('input[name="shipping_method"]:checked');
        const labelEl = document.getElementById('checkout-shipping-label');
        if (!labelEl) return;
        if (!selected) {
            labelEl.textContent = 'نوع ارسال هنوز مشخص نشده';
            labelEl.className = 'text-amber-600 font-bold';
            return;
        }
        labelEl.textContent = shippingLabels[selected.value] || 'نوع ارسال هنوز مشخص نشده';
        labelEl.className = selected.value === 'pickup' ? 'text-green-600 font-bold' : 'text-gray-700 font-bold';
    }

    function updatePaymentAndPostalRules() {
        const shipping = document.querySelector('input[name="shipping_method"]:checked');
        const isPickup = shipping?.value === 'pickup';
        const codRadio = document.getElementById('paymentCodRadio');
        const codLabel = document.getElementById('paymentCodLabel');
        const onlineRadio = document.querySelector('input[name="payment_method"][value="online"]');
        const postalInput = document.getElementById('shipping_postal_code');
        const postalRequiredMark = document.getElementById('postalRequiredMark');
        const postalOptionalNote = document.getElementById('postalOptionalNote');

        if (codRadio && codLabel) {
            codRadio.disabled = !isPickup;
            codLabel.classList.toggle('is-disabled', !isPickup);
            if (!isPickup && codRadio.checked && onlineRadio) {
                onlineRadio.checked = true;
            }
        }

        if (postalInput) {
            if (isPickup) {
                postalInput.removeAttribute('required');
            } else if (shipping) {
                postalInput.setAttribute('required', 'required');
            } else {
                postalInput.removeAttribute('required');
            }
        }
        if (postalRequiredMark) {
            postalRequiredMark.classList.toggle('hidden', isPickup || !shipping);
        }
        if (postalOptionalNote) {
            postalOptionalNote.classList.toggle('hidden', !isPickup);
        }
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
        const onlineGatewayContainer = document.getElementById('onlineGatewayContainer');
        if (onlineGatewayContainer) {
            onlineGatewayContainer.style.display = selectedPaymentMethod === 'online' ? 'block' : 'none';
        }

        syncCheckoutChoices();
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', updatePaymentAndPostalRules);
    });

    // --- Gateway selection visual sync ---
    function syncGatewayChoices() {
        document.querySelectorAll('.gateway-option-label').forEach(function(label) {
            const radio = label.querySelector('.gateway-radio');
            const iconBox = label.querySelector('.gateway-icon');
            const checkIcon = label.querySelector('.gateway-check-icon');

            if (!radio) return;

            if (radio.checked) {
                // Active selected state
                label.classList.add('border-blue-600', 'shadow-md', 'shadow-blue-100');
                label.classList.remove('border-gray-200', 'hover:border-blue-300');
                label.style.backgroundColor = 'rgba(239,246,255,0.7)';
                if (iconBox) {
                    iconBox.classList.add('bg-blue-600', 'text-white');
                    iconBox.classList.remove('bg-gray-100', 'text-gray-500');
                }
                if (checkIcon) checkIcon.classList.replace('opacity-0', 'opacity-100');
            } else {
                // Deselected state
                label.classList.remove('border-blue-600', 'shadow-md', 'shadow-blue-100');
                label.classList.add('border-gray-200', 'hover:border-blue-300');
                label.style.backgroundColor = '';
                if (iconBox) {
                    iconBox.classList.remove('bg-blue-600', 'text-white');
                    iconBox.classList.add('bg-gray-100', 'text-gray-500');
                }
                if (checkIcon) checkIcon.classList.replace('opacity-100', 'opacity-0');
            }
        });
    }

    document.querySelectorAll('.gateway-radio').forEach(radio => {
        radio.addEventListener('change', syncGatewayChoices);
    });
    // Run once on load to apply server-side checked state
    syncGatewayChoices();

    function onShippingChange() {
        updateCheckoutShippingLabel();
        updatePaymentAndPostalRules();
    }

    document.querySelectorAll('.shipping-method-radio').forEach(radio => {
        radio.addEventListener('change', onShippingChange);
    });
    onShippingChange();
    syncCheckoutChoices();

    document.querySelector('form').addEventListener('submit', function(e) {
        const shipping = document.querySelector('input[name="shipping_method"]:checked');
        if (!shipping) {
            e.preventDefault();
            alert('لطفاً روش ارسال را انتخاب کنید.');
            return;
        }

        const btn = document.getElementById('submit-order-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoading = document.getElementById('btn-loading');
        const btnIcon = document.getElementById('btn-icon');
        
        if (btn.disabled) {
            e.preventDefault();
            return;
        }

        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btn.classList.remove('hover:bg-blue-700', 'hover:scale-[1.02]');
        
        btnText.classList.add('hidden');
        btnIcon.classList.add('hidden');
        btnLoading.classList.remove('hidden');
        btnLoading.classList.add('flex', 'items-center', 'gap-2');
    });
</script>
@endsection
