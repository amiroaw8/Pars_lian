@php
    use App\Models\Setting;
    use App\Support\SmsNotifications;

    $smsBool = static fn (string $key, string $default = '1') => Setting::get($key, $default) === '1';
    $smsText = static fn (string $key, string $default = '') => Setting::get($key, $default);
@endphp

<form action="{{ route('admin.settings.update-sms') }}" method="POST" class="space-y-10">
    @csrf
    @method('PUT')

    {{-- رویدادهای ویژه — ثبت سفارش، بدهی، انبار --}}
    @php $special = $smsCategories['service_special'] ?? null; @endphp
    @if($special)
    <section>
        <div class="flex items-start gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="ti ti-{{ $special['icon'] }} text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-800">{{ $special['label'] }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $special['description'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach([
                ['key' => 'sms_order_registered', 'template' => 'sms_template_order_registered', 'label' => 'پیامک ثبت سفارش تعمیر', 'default' => SmsNotifications::defaultOrderRegisteredTemplate()],
                ['key' => 'sms_debt_notification', 'template' => 'sms_template_debt_notification', 'label' => 'پیامک اطلاع بدهی به مشتری', 'default' => SmsNotifications::defaultDebtNotificationTemplate()],
            ] as $item)
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <label class="flex items-center justify-between gap-4 cursor-pointer mb-4">
                    <span class="font-bold text-gray-800">{{ $item['label'] }}</span>
                    <input type="hidden" name="{{ $item['key'] }}" value="0">
                    <input type="checkbox" name="{{ $item['key'] }}" value="1" class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        {{ $smsBool($item['key']) ? 'checked' : '' }}>
                </label>
                <textarea name="{{ $item['template'] }}" rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    placeholder="متن پیامک...">{{ $smsText($item['template']) ?: $item['default'] }}</textarea>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- امنیت --}}
    @php $security = $smsCategories['security'] ?? null; @endphp
    @if($security)
    <section>
        <div class="flex items-start gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="ti ti-{{ $security['icon'] }} text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-800">{{ $security['label'] }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $security['description'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach([
                ['key' => 'sms_password_reset', 'template' => 'sms_template_password_reset', 'label' => 'پیامک بازیابی رمز عبور', 'default' => SmsNotifications::defaultPasswordResetTemplate()],
            ] as $item)
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <label class="flex items-center justify-between gap-4 cursor-pointer mb-4">
                    <span class="font-bold text-gray-800">{{ $item['label'] }}</span>
                    <input type="hidden" name="{{ $item['key'] }}" value="0">
                    <input type="checkbox" name="{{ $item['key'] }}" value="1" class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        {{ $smsBool($item['key']) ? 'checked' : '' }}>
                </label>
                <textarea name="{{ $item['template'] }}" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    placeholder="متن پیامک...">{{ $smsText($item['template']) ?: $item['default'] }}</textarea>
            </div>
            @endforeach

            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <label class="flex items-center justify-between gap-4 cursor-pointer mb-4">
                    <span class="font-bold text-gray-800">پیامک کد ورود دو مرحله‌ای (۲FA)</span>
                    <span class="text-xs text-gray-400">از تب «امنیت» هم قابل تنظیم است</span>
                </label>
                <textarea name="sms_template_two_factor" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    placeholder="متن پیامک...">{{ $smsText('sms_template_two_factor') ?: SmsNotifications::defaultTwoFactorTemplate() }}</textarea>
            </div>
        </div>
    </section>
    @endif

    {{-- انبار --}}
    @php $warehouse = $smsCategories['warehouse'] ?? null; @endphp
    @if($warehouse)
    <section>
        <div class="flex items-start gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="ti ti-{{ $warehouse['icon'] }} text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-800">{{ $warehouse['label'] }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $warehouse['description'] }}</p>
            </div>
        </div>

        <div class="mb-4">
            <label class="flex items-center justify-between gap-4 cursor-pointer p-4 bg-white rounded-xl border border-gray-200 max-w-md">
                <span class="font-bold text-gray-800">فعال‌سازی هشدار موجودی انبار</span>
                <input type="hidden" name="sms_inventory_alert" value="0">
                <input type="checkbox" name="sms_inventory_alert" value="1" class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    {{ $smsBool('sms_inventory_alert') ? 'checked' : '' }}>
            </label>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <h4 class="font-bold text-gray-800 mb-3">هشدار تک‌کالا</h4>
                <textarea name="sms_template_inventory_item" rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $smsText('sms_template_inventory_item') ?: SmsNotifications::defaultInventoryItemAlertTemplate() }}</textarea>
            </div>
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <h4 class="font-bold text-gray-800 mb-3">گزارش دوره‌ای چند کالا</h4>
                <textarea name="sms_template_inventory_batch" rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $smsText('sms_template_inventory_batch') ?: SmsNotifications::defaultInventoryBatchAlertTemplate() }}</textarea>
            </div>
        </div>
    </section>
    @endif

    {{-- تغییر وضعیت سفارش تعمیر --}}
    @php $serviceStatus = $smsCategories['service_status'] ?? null; @endphp
    @if($serviceStatus)
    <section>
        <div class="flex items-start gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i class="ti ti-{{ $serviceStatus['icon'] }} text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-800">{{ $serviceStatus['label'] }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $serviceStatus['description'] }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($serviceStatus['variables'] as $var)
                        <code class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-xs">{{ $var['token'] }}</code>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($smsStatuses as $status)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-colors">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $status->color ?? '#94a3b8' }};"></span>
                        <h4 class="font-bold text-gray-800 truncate">{{ $status->label ?: $status->id }}</h4>
                    </div>
                    <label class="flex items-center gap-2 shrink-0 cursor-pointer text-xs font-bold text-gray-600">
                        <input type="checkbox" name="sms_enabled[{{ $status->id }}]" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ ($status->sms_enabled ?? true) ? 'checked' : '' }}>
                        ارسال
                    </label>
                </div>
                <textarea name="templates[{{ $status->id }}]" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    placeholder="متن پیامک برای «{{ $status->label }}»...">{{ $status->sms_template }}</textarea>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- فروشگاه — وضعیت سفارش --}}
    @php $shopOrder = $smsCategories['shop_order'] ?? null; @endphp
    @if($shopOrder && $shopOrderStatuses->isNotEmpty())
    <section>
        <div class="flex items-start gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <i class="ti ti-{{ $shopOrder['icon'] }} text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-800">{{ $shopOrder['label'] }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $shopOrder['description'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($shopOrderStatuses as $status)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h4 class="font-bold text-gray-800">{{ $status->label ?: $status->id }}</h4>
                    <label class="flex items-center gap-2 shrink-0 cursor-pointer text-xs font-bold text-gray-600">
                        <input type="checkbox" name="shop_order_sms_enabled[{{ $status->id }}]" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ ($status->sms_enabled ?? true) ? 'checked' : '' }}>
                        ارسال
                    </label>
                </div>
                <textarea name="shop_order_templates[{{ $status->id }}]" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $status->sms_template }}</textarea>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- فروشگاه — وضعیت پرداخت --}}
    @php $shopPayment = $smsCategories['shop_payment'] ?? null; @endphp
    @if($shopPayment && $shopPaymentStatuses->isNotEmpty())
    <section>
        <div class="flex items-start gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i class="ti ti-{{ $shopPayment['icon'] }} text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-800">{{ $shopPayment['label'] }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $shopPayment['description'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($shopPaymentStatuses as $status)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h4 class="font-bold text-gray-800">{{ $status->label ?: $status->id }}</h4>
                    <label class="flex items-center gap-2 shrink-0 cursor-pointer text-xs font-bold text-gray-600">
                        <input type="checkbox" name="shop_payment_sms_enabled[{{ $status->id }}]" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ ($status->sms_enabled ?? true) ? 'checked' : '' }}>
                        ارسال
                    </label>
                </div>
                <textarea name="shop_payment_templates[{{ $status->id }}]" rows="3"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $status->sms_template }}</textarea>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- اطلاعات تماس در پیامک --}}
    <section class="bg-blue-50 rounded-xl p-6 border border-blue-100">
        <h3 class="text-lg font-black text-gray-800 mb-4">اطلاعات تماس در پیامک‌ها</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">تلفن تماس</label>
                <input type="text" name="sms_business_phone" value="{{ $smsText('sms_business_phone') }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    placeholder="مثلاً 021-12345678">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">آدرس</label>
                <input type="text" name="sms_business_address" value="{{ $smsText('sms_business_address') }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    placeholder="آدرس مرکز">
            </div>
        </div>
    </section>

    <div class="flex justify-end sticky bottom-4 pt-4">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-colors duration-200 flex items-center">
            <i class="ti ti-device-floppy me-2"></i>
            ذخیره تنظیمات پیامک
        </button>
    </div>
</form>
