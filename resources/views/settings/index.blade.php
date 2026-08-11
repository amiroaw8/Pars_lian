@extends('layouts.admin')

@section('title', 'تنظیمات سیستم - پارس لیان')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">تنظیمات سیستم</h1>
            <p class="text-gray-500 mt-2">مدیریت متن‌های چاپی و قالب‌های پیامک</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
            <p class="font-bold">موفقیت</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Tabs Header -->
        <div class="flex border-b border-gray-200 overflow-x-auto whitespace-nowrap">

            <button onclick="switchTab('print')" id="tab-print" class="px-6 py-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600 focus:outline-none transition-colors duration-200">
                <i class="ti ti-printer me-2"></i>تنظیمات چاپ
            </button>
            <button onclick="switchTab('sms')" id="tab-sms" class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition-colors duration-200">
                <i class="ti ti-message me-2"></i>قالب‌های پیامک
            </button>
            @if(auth()->user()->isSuperAdmin())
            <button onclick="switchTab('security')" id="tab-security" class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition-colors duration-200">
                <i class="ti ti-shield-lock me-2"></i>امنیت
            </button>
            <button onclick="switchTab('service')" id="tab-service" class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition-colors duration-200">
                <i class="ti ti-tool me-2"></i>پذیرش و تکنسین
            </button>
            <button onclick="switchTab('payment-gateways')" id="tab-payment-gateways" class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition-colors duration-200">
                <i class="ti ti-credit-card me-2"></i>درگاه‌های پرداخت آنلاین
            </button>
            <button onclick="switchTab('licenses')" id="tab-licenses" class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition-colors duration-200">
                <i class="ti ti-certificate me-2"></i>مجوزات سایت
            </button>
            @endif
        </div>

        <!-- Tab Content: Print Settings -->
        <div id="content-print" class="p-6">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-8">
                    @foreach($printSettings as $group => $settings)
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                                @switch($group)
                                    @case('print_header')
                                        سربرگ (Header)
                                        @break
                                    @case('print_footer')
                                        پاورقی (Footer)
                                        @break
                                    @case('print_receipt')
                                        رسید پذیرش
                                        @break
                                    @case('print_invoice')
                                        فاکتور
                                        @break
                                    @case('print_thermal')
                                        چاپ حرارتی
                                        @break
                                    @default
                                        {{ $group }}
                                @endswitch
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-6">
                                @foreach($settings as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $setting->label }}
                                        </label>
                                        
                                        @if($setting->type === 'textarea')
                                            <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" rows="4" 
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            >{{ $setting->value }}</textarea>
                                        @else
                                            <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}" value="{{ $setting->value }}"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition-colors duration-200 flex items-center">
                        <i class="ti ti-device-floppy me-2"></i>
                        ذخیره تنظیمات چاپ
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab Content: SMS Templates -->
        <div id="content-sms" class="p-6 hidden">
            @include('settings.partials.sms-tab')
        </div>

        @if(auth()->user()->isSuperAdmin())
        <div id="content-security" class="p-6 hidden">
            <form action="{{ route('admin.settings.update-security') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 max-w-2xl">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">تایید دو مرحله‌ای (SMS)</h3>
                    <p class="text-sm text-gray-600 mb-6">با غیرفعال کردن این گزینه، هنگام ورود کارکنان (مدیر، پذیرش، تعمیرکار، انبار، حسابدار) کد پیامکی ارسال نمی‌شود.</p>
                    @php $twoFactorOn = (\App\Models\Setting::get('two_factor_enabled', '1') === '1'); @endphp
                    <label class="flex items-center justify-between cursor-pointer p-4 bg-white rounded-xl border border-gray-200">
                        <span class="font-bold text-gray-800">فعال‌سازی ۲FA برای همه کارکنان</span>
                        <input type="hidden" name="two_factor_enabled" value="0">
                        <input type="checkbox" name="two_factor_enabled" value="1" {{ $twoFactorOn ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </label>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow">
                        ذخیره تنظیمات امنیتی
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Tab Content: Service & Technicians -->
        <div id="content-service" class="p-6 hidden">
            <form action="{{ route('admin.settings.update-service') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 max-w-2xl">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">پذیرش و تکنسین</h3>
                    <p class="text-sm text-gray-600 mb-6">کاربرانی که مجاز به دریافت ارجاع به عنوان تعمیرکار هستند را انتخاب کنید.</p>
                    
                    <div class="space-y-3 max-h-96 overflow-y-auto p-2">
                        @foreach($serviceStaffCandidates as $candidate)
                            <label class="flex items-center space-x-3 space-x-reverse p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="assignable_technician_ids[]" value="{{ $candidate->id }}"
                                    {{ in_array($candidate->id, $selectedAssignableTechnicianIds) ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <div>
                                    <span class="block text-sm font-medium text-gray-900">{{ trim($candidate->name . ' ' . $candidate->family) }}</span>
                                </div>
                            </label>
                        @endforeach
                        
                        @if($serviceStaffCandidates->isEmpty())
                            <p class="text-sm text-gray-500 italic">هیچ کاربری با نقش تعمیرکار یا مدیر یافت نشد.</p>
                        @endif
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow">
                        ذخیره تنظیمات پذیرش
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab Content: Licenses -->
        <div id="content-licenses" class="p-6 hidden">
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-100 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">افزودن مجوز جدید</h3>
                <form action="{{ route('admin.settings.add-license') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">تصویر مجوز (نماد)</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control w-full bg-white border border-gray-300 rounded-lg p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">لینک هدایت (URL)</label>
                            <input type="url" name="url" placeholder="https://trustseal.enamad.ir/..." class="form-control w-full bg-white border border-gray-300 rounded-lg p-2 h-[42px]" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow">
                            <i class="ti ti-plus me-1"></i> افزودن مجوز
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">مجوزهای ثبت شده</h3>
                @if(count($siteLicenses) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($siteLicenses as $index => $license)
                            <div class="border border-gray-200 rounded-xl p-4 text-center relative hover:shadow-md transition-shadow">
                                <img src="{{ asset($license['image']) }}" alt="License" class="mx-auto h-24 object-contain mb-4">
                                <a href="{{ $license['url'] }}" target="_blank" class="text-sm text-blue-600 hover:underline break-all mb-4 block" dir="ltr">{{ \Illuminate\Support\Str::limit($license['url'], 30) }}</a>
                                <form action="{{ route('admin.settings.remove-license', $index) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm w-full border border-red-200 rounded-lg py-1 hover:bg-red-50 transition-colors" onclick="return confirm('آیا از حذف این مجوز اطمینان دارید؟')">
                                        <i class="ti ti-trash me-1"></i> حذف
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">هیچ مجوزی ثبت نشده است.</p>
                @endif
            </div>
        </div>

        <div id="content-payment-gateways" class="p-6 hidden">
            @include('settings.partials.payment-gateways-tab')
        </div>
        @endif
    </div>
</div>

<script>
    function switchTab(tabName) {
        ['print', 'sms', 'security', 'service', 'payment-gateways', 'licenses'].forEach(function(name) {
            const tab = document.getElementById('tab-' + name);
            const content = document.getElementById('content-' + name);
            if (!tab || !content) return;
            tab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            tab.classList.add('text-gray-500');
            content.classList.add('hidden');
        });

        const activeTab = document.getElementById('tab-' + tabName);
        const activeContent = document.getElementById('content-' + tabName);
        if (activeTab && activeContent) {
            activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            activeTab.classList.remove('text-gray-500');
            activeContent.classList.remove('hidden');
        }
    }
</script>
@endsection
