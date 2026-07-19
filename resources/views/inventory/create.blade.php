@extends('layouts.admin')

@section('title', 'افزودن کالا به انبار - پارس لیان')

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
                        <i class="ti ti-package-plus text-primary-400"></i>
                        ثبت کالای جدید
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">افزودن به انبار</h2>
                    <p class="text-slate-300 text-lg font-medium max-w-xl leading-relaxed">ثبت اطلاعات قطعات، دستگاه‌ها و ابزارهای جدید در سیستم کنترل موجودی.</p>
                </div>
                <div class="flex flex-col md:flex-row gap-4">
                    <a href="{{ route('automation.inventory.index') }}" class="btn-modern btn-modern-light py-4 px-8 group">
                        <i class="ti ti-arrow-right group-hover:-translate-x-1 transition-transform"></i>
                        <span>بازگشت به لیست</span>
                    </a>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-primary-500/20 transition-colors duration-700"></div>
        </div>

        <form method="POST" action="{{ route('automation.inventory.store') }}" id="inventory-form" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-8">
                    <x-enhanced-card title="اطلاعات پایه کالا" icon="ti-tag" animated>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-2">
                            <div class="form-group-modern md:col-span-2 group">
                                <label for="name" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-info-circle"></i>
                                    نام کالا
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="name"
                                        name="name" 
                                        class="form-control-modern pr-12 @error('name') border-danger-500 @enderror"
                                        placeholder="نام کامل کالا را وارد کنید"
                                        value="{{ old('name') }}"
                                        required
                                        autofocus
                                    >
                                    <i class="ti ti-package absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('name')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label for="sku" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-barcode"></i>
                                    کد کالا (SKU)
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="sku"
                                        name="sku" 
                                        class="form-control-modern pr-12 @error('sku') border-danger-500 @enderror"
                                        placeholder="مثال: PROD-12345"
                                        value="{{ old('sku') }}"
                                    >
                                    <i class="ti ti-scan absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('sku')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label for="device_code" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-device-mobile"></i>
                                    کد دستگاه مرتبط
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="device_code"
                                        name="device_code" 
                                        class="form-control-modern pr-12 @error('device_code') border-danger-500 @enderror"
                                        placeholder="مثال: IPHONE-13-PRO"
                                        value="{{ old('device_code') }}"
                                    >
                                    <i class="ti ti-smartphone absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>
                            
                            <div class="form-group-modern group">
                                <label for="type" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-category"></i>
                                    نوع کالا
                                </label>
                                <div class="relative">
                                    <select name="type" id="type" class="form-control-modern pr-12 appearance-none @error('type') border-danger-500 @enderror" required>
                                        <option value="device" {{ old('type') == 'device' ? 'selected' : '' }}>💻 دستگاه</option>
                                        <option value="part" {{ old('type') == 'part' ? 'selected' : '' }}>🧩 قطعه</option>
                                        <option value="tool" {{ old('type') == 'tool' ? 'selected' : '' }}>🛠️ ابزار</option>
                                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>📦 سایر</option>
                                    </select>
                                    <i class="ti ti-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none transition-transform group-focus-within:rotate-180"></i>
                                    <i class="ti ti-layers-intersect absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('type')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label for="condition" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-activity-heartbeat"></i>
                                    وضعیت کالا
                                </label>
                                <div class="relative">
                                    <select name="condition" id="condition" class="form-control-modern pr-12 appearance-none @error('condition') border-danger-500 @enderror" required>
                                        <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>✨ نو (New)</option>
                                        <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>♻️ دست دوم (Used)</option>
                                    </select>
                                    <i class="ti ti-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none transition-transform group-focus-within:rotate-180"></i>
                                    <i class="ti ti-star absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('condition')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <div class="form-group-modern group">
                                <label for="color" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-palette"></i>
                                    رنگ (اختیاری)
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="color"
                                        name="color" 
                                        class="form-control-modern pr-12"
                                        placeholder="مثلاً: مشکی، نقره‌ای"
                                        value="{{ old('color') }}"
                                    >
                                    <i class="ti ti-palette absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>

                            <div class="form-group-modern group">
                                <label for="rack_location" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-map-pin"></i>
                                    موقعیت در انبار
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="rack_location"
                                        name="rack_location" 
                                        class="form-control-modern pr-12 @error('rack_location') border-danger-500 @enderror"
                                        placeholder="مثال: قفسه A - ردیف 3"
                                        value="{{ old('rack_location') }}"
                                    >
                                    <i class="ti ti-location absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>
                            
                            <div class="form-group-modern md:col-span-2 group">
                                <label for="description" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-file-description"></i>
                                    توضیحات تکمیلی
                                </label>
                                <div class="relative">
                                    <textarea 
                                        id="description"
                                        name="description" 
                                        class="form-control-modern pr-12 h-32 resize-none @error('description') border-danger-500 @enderror"
                                        placeholder="توضیحات مربوط به کالا..."
                                    >{{ old('description') }}</textarea>
                                    <i class="ti ti-align-right absolute right-4 top-4 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>

                            <div class="form-group-modern md:col-span-2 group">
                                <label for="compatibility_notes" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-notes"></i>
                                    یادداشت‌های سازگاری
                                </label>
                                <div class="relative">
                                    <textarea 
                                        id="compatibility_notes"
                                        name="compatibility_notes" 
                                        class="form-control-modern pr-12 h-24 resize-none @error('compatibility_notes') border-danger-500 @enderror"
                                        placeholder="جزئیات سازگاری با مدل‌های مختلف..."
                                    >{{ old('compatibility_notes') }}</textarea>
                                    <i class="ti ti-notebook absolute right-4 top-4 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                            </div>
                        </div>
                    </x-enhanced-card>
                </div>

                <!-- Stock & Price -->
                <div class="space-y-8">
                    <x-enhanced-card title="موجودی و قیمت" icon="ti-database" animated>
                        <div class="space-y-6 p-2">
                            <div class="form-group-modern group">
                                <label for="quantity" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-stack"></i>
                                    موجودی اولیه
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        id="quantity"
                                        name="quantity" 
                                        class="form-control-modern pr-12 text-center font-black"
                                        value="{{ old('quantity', 0) }}" 
                                        min="0" 
                                        required
                                    >
                                    <i class="ti ti-box-padding absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary-500"></i>
                                </div>
                                @error('quantity')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            
                            <div class="form-group-modern group">
                                <label for="min_quantity" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-alert-triangle"></i>
                                    حداقل موجودی (هشدار)
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        id="min_quantity"
                                        name="min_quantity" 
                                        class="form-control-modern pr-12 text-center font-black border-amber-200 bg-amber-50/30"
                                        value="{{ old('min_quantity', 5) }}" 
                                        min="0" 
                                        required
                                    >
                                    <i class="ti ti-bell-ringing absolute right-4 top-1/2 -translate-y-1/2 text-amber-500"></i>
                                </div>
                                @error('min_quantity')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label for="price" class="form-label-modern group-focus-within:text-primary-600">
                                    <i class="ti ti-coin"></i>
                                    قیمت واحد (تومان)
                                </label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        id="price"
                                        name="price" 
                                        class="form-control-modern pr-12 text-center font-black text-emerald-600"
                                        placeholder="قیمت به تومان"
                                        value="{{ old('price') }}"
                                        min="0" 
                                        required
                                    >
                                    <i class="ti ti-currency-toman absolute right-4 top-1/2 -translate-y-1/2 text-emerald-500"></i>
                                </div>
                                @error('price')
                                    <p class="mt-2 text-xs text-danger-500 font-bold flex items-center gap-1 animate-shake">
                                        <i class="ti ti-alert-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 pt-8 border-t border-slate-100 space-y-4 p-2">
                            <button type="submit" class="btn-modern btn-modern-primary w-full py-4 justify-center shadow-xl shadow-primary-500/20 group">
                                <i class="ti ti-device-floppy group-hover:scale-110 transition-transform"></i>
                                <span>ثبت و افزودن به انبار</span>
                            </button>
                            <a href="{{ route('automation.inventory.index') }}" class="btn-modern btn-modern-light w-full py-4 justify-center">
                                <i class="ti ti-x"></i>
                                <span>انصراف</span>
                            </a>
                        </div>
                    </x-enhanced-card>

                    <!-- Help Card -->
                    <x-enhanced-card variant="warning" title="نکات مهم ثبت" icon="ti-bulb" animated>
                        <div class="space-y-4">
                            <div class="flex gap-4 p-3 rounded-2xl bg-white/50 border border-white/80">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                                    <i class="ti ti-alert-circle"></i>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-xs font-black text-slate-800">حداقل موجودی</h4>
                                    <p class="text-[11px] text-slate-500 leading-relaxed">سیستم زمانی که موجودی به این مقدار برسد، در داشبورد هشدار خواهد داد.</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-4 p-3 rounded-2xl bg-white/50 border border-white/80">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                                    <i class="ti ti-currency-dollar"></i>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-xs font-black text-slate-800">قیمت‌گذاری</h4>
                                    <p class="text-[11px] text-slate-500 leading-relaxed">قیمت وارد شده به عنوان قیمت پایه در فاکتورهای فروش و تعمیرات استفاده می‌شود.</p>
                                </div>
                            </div>
                        </div>
                    </x-enhanced-card>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Add loading state to form submission
    document.getElementById('inventory-form').addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = `
            <i class="ti ti-loader animate-spin"></i>
            <span>در حال ثبت اطلاعات...</span>
        `;
    });
</script>
@endsection
