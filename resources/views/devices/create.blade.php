@extends('layouts.admin')

@section('title', 'ثبت دستگاه جدید - پارس لیان')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-blue-500/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-device-laptop text-amber-400"></i>
                        افزودن به بانک فنی
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">ثبت دستگاه جدید</h2>
                    <p class="text-blue-100 text-lg font-medium max-w-xl leading-relaxed">با ثبت دقیق مشخصات فنی دستگاه، مدیریت سوابق تعمیراتی و گارانتی آن با دقت بیشتری انجام خواهد شد.</p>
                </div>
                <div class="flex flex-shrink-0">
                    <div class="w-24 h-24 md:w-40 md:h-40 bg-white/20 backdrop-blur-xl rounded-[2.5rem] flex items-center justify-center text-white border border-white/30 shadow-2xl animate-float group-hover:scale-110 transition-transform duration-500">
                        <i class="ti ti-plus text-6xl md:text-8xl drop-shadow-lg"></i>
                    </div>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/20 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-400/20 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-blue-400/30 transition-colors duration-700"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <x-enhanced-card variant="default" title="مشخصات فنی دستگاه" icon="settings" class="animate-slide-up">
                    <form action="{{ route('automation.devices.store') }}" method="POST" class="space-y-10">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Customer Select -->
                            <div class="form-group-modern group md:col-span-2">
                                <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">انتخاب مالک (مشتری)</label>
                                <div class="relative">
                                    <i class="ti ti-user-check absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                    <select name="customer_id" class="form-control-modern pr-12 @error('customer_id') border-rose-500 @enderror" required>
                                        <option value="">— جستجو و انتخاب مشتری —</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ (old('customer_id') == $customer->id || request('customer_id') == $customer->id) ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->phone }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('customer_id') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <!-- Type -->
                            <div class="form-group-modern group">
                                <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">نوع دستگاه</label>
                                <div class="relative">
                                    <i class="ti ti-category absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                    <input type="text" name="type" class="form-control-modern pr-12 @error('type') border-rose-500 @enderror" value="{{ old('type') }}" required placeholder="مثال: لپ‌تاپ، پرینتر، موبایل">
                                </div>
                                @error('type') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <!-- Model -->
                            <div class="form-group-modern group">
                                <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">مدل دقیق دستگاه</label>
                                <div class="relative">
                                    <i class="ti ti-device-laptop absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                    <input type="text" name="model" class="form-control-modern pr-12 @error('model') border-rose-500 @enderror" value="{{ old('model') }}" required placeholder="مثال: ASUS VivoBook 15">
                                </div>
                                @error('model') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <!-- Serial Number -->
                            <div class="form-group-modern group">
                                <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">شماره سریال (S/N)</label>
                                <div class="relative">
                                    <i class="ti ti-barcode absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                    <input type="text" name="serial_number" class="form-control-modern pr-12 @error('serial_number') border-rose-500 @enderror" value="{{ old('serial_number') }}" placeholder="شماره سریال دستگاه">
                                </div>
                                @error('serial_number') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <!-- Asset Number -->
                            <div class="form-group-modern group">
                                <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">شماره اموال (اختیاری)</label>
                                <div class="relative">
                                    <i class="ti ti-hash absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                    <input type="text" name="asset_number" class="form-control-modern pr-12 @error('asset_number') border-rose-500 @enderror" value="{{ old('asset_number') }}" placeholder="شماره اموال سازمانی">
                                </div>
                                @error('asset_number') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <!-- Guarantee -->
                            <div class="md:col-span-2">
                                <div class="p-6 rounded-[2rem] bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl transition-all duration-500 group/check">
                                    <label class="flex items-center gap-4 cursor-pointer">
                                        <div class="relative flex items-center">
                                            <input type="hidden" name="has_guarantee" value="0">
                                            <input type="checkbox" name="has_guarantee" value="1" {{ old('has_guarantee') ? 'checked' : '' }} class="w-6 h-6 rounded-lg border-slate-300 text-blue-600 focus:ring-blue-500 transition-all cursor-pointer">
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-900 group-hover/check:text-blue-600 transition-colors">این دستگاه دارای گارانتی فعال می‌باشد</span>
                                            <span class="text-[10px] text-slate-400 font-medium">با فعال‌سازی این گزینه، هزینه‌های تعمیر طبق ضوابط گارانتی محاسبه خواهد شد.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row items-center gap-4 pt-10 border-t border-slate-100">
                            <button type="submit" class="btn-modern btn-modern-primary w-full md:w-auto py-4 px-12 shadow-xl shadow-blue-500/20 group">
                                <span>ثبت و ذخیره نهایی دستگاه</span>
                                <i class="ti ti-device-floppy group-hover:scale-125 transition-transform"></i>
                            </button>
                            <a href="{{ route('automation.devices.index') }}" class="btn-modern btn-modern-light w-full md:w-auto py-4 px-12">
                                <span>انصراف و بازگشت</span>
                            </a>
                        </div>
                    </form>
                </x-enhanced-card>
            </div>

            <div class="lg:col-span-1 space-y-8">
                <x-enhanced-card variant="default" title="راهنمای ثبت هوشمند" icon="bulb" class="animate-slide-up" style="animation-delay: 0.1s">
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-info-circle text-xl"></i>
                            </div>
                            <p class="text-xs text-slate-600 font-medium leading-relaxed">
                                ثبت دقیق مدل و شماره سریال دستگاه به شما کمک می‌کند تا در مراجعات بعدی، سوابق قطعات و تعمیرات را سریع‌تر بازیابی کنید.
                            </p>
                        </div>

                        <div class="p-6 rounded-[2rem] bg-amber-50 border border-amber-100/50">
                            <h4 class="text-sm font-black text-amber-800 mb-4 flex items-center gap-2">
                                <i class="ti ti-alert-triangle"></i>
                                نکات الزامی
                            </h4>
                            <ul class="space-y-3">
                                <li class="flex items-start gap-2 text-[10px] text-amber-700 font-bold leading-relaxed">
                                    <div class="w-1.5 h-1.5 rounded-full bg-amber-400 mt-1 flex-shrink-0"></div>
                                    انتخاب مشتری برای انتساب مالکیت دستگاه الزامی است.
                                </li>
                                <li class="flex items-start gap-2 text-[10px] text-amber-700 font-bold leading-relaxed">
                                    <div class="w-1.5 h-1.5 rounded-full bg-amber-400 mt-1 flex-shrink-0"></div>
                                    مدل دستگاه را دقیقاً مطابق با برچسب مشخصات فنی وارد کنید.
                                </li>
                            </ul>
                        </div>

                        <div class="p-6 rounded-[2rem] bg-blue-50/50 border border-blue-100/50">
                            <h4 class="text-sm font-black text-blue-800 mb-3 flex items-center gap-2">
                                <i class="ti ti-history"></i>
                                پس از ثبت چه می‌شود؟
                            </h4>
                            <p class="text-[10px] text-blue-600 font-medium leading-relaxed">
                                پس از ثبت دستگاه، می‌توانید بلافاصله برای آن سفارش تعمیر یا سرویس جدید ایجاد کنید. همچنین تمامی سوابق این دستگاه به صورت دائمی در پرونده مشتری حفظ خواهد شد.
                            </p>
                        </div>
                    </div>
                </x-enhanced-card>
            </div>
        </div>
    </div>
</div>
@endsection
