<form action="{{ route('admin.settings.update-payment-gateways') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="space-y-8">
        <!-- Notice Box -->
        <div class="bg-blue-50 border-r-4 border-blue-600 p-5 rounded-xl text-sm text-blue-900 leading-relaxed flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                <i class="ti ti-info-circle text-2xl"></i>
            </div>
            <div>
                <h4 class="font-black text-base text-blue-950 mb-1">راهنمای درگاه‌های پرداخت آنلاین</h4>
                <p>شما می‌توانید درگاه‌های پرداخت آنلاین مورد نیاز را فعال کرده و کلیدهای ارتباطی آن‌ها را وارد نمایید. درگاه **زرین‌پال** به صورت پیش‌فرض در حالت **سندباکس (تست)** فعال است تا بتوانید مراحل خرید را بدون نیاز به مرچنت واقعی تست نمایید.</p>
            </div>
        </div>

        <!-- Default Gateway Selector -->
        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 shadow-sm">
            <h3 class="text-lg font-black text-gray-900 mb-4 flex items-center gap-2">
                <i class="ti ti-star-filled text-amber-500 text-xl"></i>
                درگاه پرداخت پیش‌فرض سایت
            </h3>
            <p class="text-xs text-gray-500 mb-4">در صورتی که چند درگاه آنلاین فعال باشند، این درگاه به عنوان گزینه‌ی انتخابی اولیه مشتریان در برگه تسویه حساب قرار خواهد گرفت.</p>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                @foreach($paymentSettings['gateways'] as $driver => $config)
                    <label class="relative flex items-center p-3 border-2 rounded-xl cursor-pointer bg-white transition-all hover:border-blue-400 border-gray-200">
                        <input type="radio" name="default" value="{{ $driver }}" class="sr-only" {{ ($paymentSettings['default'] ?? 'zarinpal') === $driver ? 'checked' : '' }}>
                        <div class="w-7 h-7 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center ml-2 shrink-0 group-has-[:checked]:bg-blue-600 group-has-[:checked]:text-white">
                            <i class="{{ $config['icon'] ?? 'ti ti-credit-card' }}"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-900 truncate">{{ $config['name'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Gateways Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($supportedGatewayDrivers as $driver => $meta)
                @php
                    $config = $paymentSettings['gateways'][$driver] ?? $meta['defaults'];
                    $isEnabled = !empty($config['enabled']);
                @endphp
                <div class="bg-white rounded-2xl border-2 {{ $isEnabled ? 'border-blue-200 shadow-md shadow-blue-500/5' : 'border-gray-200 opacity-80' }} transition-all overflow-hidden">
                    <!-- Header -->
                    <div class="p-5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 {{ $isEnabled ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }} rounded-xl flex items-center justify-center text-xl transition-colors">
                                <i class="{{ $meta['icon'] }}"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 text-base">{{ $meta['name'] }}</h4>
                                <span class="text-[11px] font-medium text-gray-400" dir="ltr">driver: {{ $driver }}</span>
                            </div>
                        </div>

                        <!-- Toggle Switch -->
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="gateways[{{ $driver }}][enabled]" value="0">
                            <input type="checkbox" name="gateways[{{ $driver }}][enabled]" value="1" {{ $isEnabled ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-4">
                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">توضیحات درگاه (برای مشتری)</label>
                            <input type="text" name="gateways[{{ $driver }}][description]" value="{{ $config['description'] ?? '' }}" class="w-full text-xs bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                        </div>

                        <!-- Specific Fields -->
                        @foreach($meta['fields'] as $fieldKey => $fieldMeta)
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ $fieldMeta['label'] }}</label>
                                @if($fieldMeta['type'] === 'select')
                                    <select name="gateways[{{ $driver }}][{{ $fieldKey }}]" class="w-full text-xs bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                                        @foreach($fieldMeta['options'] as $optVal => $optLabel)
                                            <option value="{{ $optVal }}" {{ ($config[$fieldKey] ?? '') == $optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="{{ $fieldMeta['type'] }}" name="gateways[{{ $driver }}][{{ $fieldKey }}]" value="{{ $config[$fieldKey] ?? '' }}" placeholder="{{ $fieldMeta['placeholder'] ?? '' }}" class="w-full text-xs bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all" dir="ltr">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3.5 px-8 rounded-2xl shadow-lg shadow-blue-500/20 hover:scale-[1.02] transition-all flex items-center gap-2 text-base">
                <i class="ti ti-device-floppy text-xl"></i>
                ذخیره تنظیمات درگاه‌های پرداخت
            </button>
        </div>
    </div>
</form>
