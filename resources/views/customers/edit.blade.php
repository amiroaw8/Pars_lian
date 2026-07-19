@extends('layouts.admin')

@section('title', 'ویرایش مشتری - پارس لیان')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-orange-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-yellow-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="space-y-8 relative z-10">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 via-amber-600 to-orange-700 rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-amber-500/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-edit text-white"></i>
                        به‌روزرسانی اطلاعات
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">ویرایش مشتری: {{ $customer->name }}</h2>
                    <p class="text-amber-50 text-lg font-medium max-w-xl leading-relaxed">در این بخش می‌توانید اطلاعات تماس و آدرس مشتری را اصلاح و به‌روزرسانی کنید.</p>
                </div>
                <div class="flex flex-shrink-0">
                    <div class="w-24 h-24 md:w-40 md:h-40 bg-white/20 backdrop-blur-xl rounded-[2.5rem] flex items-center justify-center text-white border border-white/30 shadow-2xl animate-float group-hover:scale-110 transition-transform duration-500">
                        <i class="ti ti-user-edit text-6xl md:text-8xl drop-shadow-lg"></i>
                    </div>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/20 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-amber-400/20 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-amber-400/30 transition-colors duration-700"></div>
        </div>

        <div class="max-w-4xl mx-auto">
            <x-enhanced-card title="اصلاح اطلاعات مشتری" icon="edit" variant="warning" class="animate-slide-up">
                <x-slot name="actions">
                    <a href="{{ route('automation.customers.show', $customer) }}" class="btn-modern btn-modern-light py-2 px-6 text-sm group">
                        <i class="ti ti-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        <span>بازگشت به جزئیات</span>
                    </a>
                </x-slot>

                <form method="POST" action="{{ route('automation.customers.update', $customer) }}" class="space-y-10">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="form-group-modern group">
                            <label for="name" class="form-label-modern group-focus-within:text-amber-600 transition-colors">
                                <i class="ti ti-user text-lg"></i>
                                نام و نام خانوادگی مشتری
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="name"
                                    name="name" 
                                    class="form-control-modern focus:border-amber-500 focus:ring-amber-200 @error('name') border-rose-500 @enderror"
                                    placeholder="نام کامل مشتری"
                                    value="{{ old('name', $customer->name) }}"
                                    required
                                    autofocus
                                >
                            </div>
                            @error('name')
                                <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1 animate-shake">
                                    <i class="ti ti-alert-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <div class="form-group-modern group">
                            <label for="phone" class="form-label-modern group-focus-within:text-amber-600 transition-colors">
                                <i class="ti ti-phone text-lg"></i>
                                شماره همراه
                            </label>
                            <div class="relative">
                                <input 
                                    type="tel" 
                                    id="phone"
                                    name="phone" 
                                    class="form-control-modern text-left dir-ltr font-black tracking-widest focus:border-amber-500 focus:ring-amber-200 @error('phone') border-rose-500 @enderror"
                                    placeholder="09123456789"
                                    value="{{ old('phone', $customer->phone) }}"
                                    required
                                >
                            </div>
                            @error('phone')
                                <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1 animate-shake">
                                    <i class="ti ti-alert-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password Management Section -->
                        @if(auth()->user()->canManageCustomers() || auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                        <div class="form-group-modern group md:col-span-2">
                            <label class="form-label-modern text-amber-600 flex items-center gap-2 mb-4">
                                <i class="ti ti-lock text-lg"></i>
                                مدیریت رمز ورود (اختیاری)
                            </label>
                            <div class="bg-amber-50/50 border border-amber-100 rounded-2xl p-4 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group-modern group">
                                        <label for="password" class="form-label-modern group-focus-within:text-amber-600 transition-colors">
                                            <i class="ti ti-key text-lg"></i>
                                            رمز ورود جدید
                                        </label>
                                        <div class="relative">
                                            <input 
                                                type="password" 
                                                id="password"
                                                name="password" 
                                                class="form-control-modern focus:border-amber-500 focus:ring-amber-200 @error('password') border-rose-500 @enderror"
                                                placeholder="رمز ورود جدید (اگر می‌خواهید تغییر دهید)"
                                            >
                                        </div>
                                        @error('password')
                                            <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1 animate-shake">
                                                <i class="ti ti-alert-circle"></i>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group-modern group">
                                        <label for="password_confirmation" class="form-label-modern group-focus-within:text-amber-600 transition-colors">
                                            <i class="ti ti-key-off text-lg"></i>
                                            تأیید رمز ورود
                                        </label>
                                        <div class="relative">
                                            <input 
                                                type="password" 
                                                id="password_confirmation"
                                                name="password_confirmation" 
                                                class="form-control-modern focus:border-amber-500 focus:ring-amber-200"
                                                placeholder="تأیید رمز ورود"
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-amber-700 bg-amber-100/50 rounded-xl p-3 flex items-start gap-2">
                                    <i class="ti ti-info-circle mt-0.5 flex-shrink-0"></i>
                                    <span>اگر نمی‌خواهید رمز را تغییر دهید، این فیلدها را خالی بگذارید. درصورت تغییر، مشتری می‌تواند با رمز جدید وارد شود.</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="form-group-modern md:col-span-2 group">
                            <label for="address" class="form-label-modern group-focus-within:text-amber-600 transition-colors">
                                <i class="ti ti-map-pin text-lg"></i>
                                آدرس دقیق محل سکونت / شرکت
                            </label>
                            <div class="relative">
                                <textarea 
                                    id="address"
                                    name="address" 
                                    class="form-control-modern min-h-[120px] py-4 focus:border-amber-500 focus:ring-amber-200 @error('address') border-rose-500 @enderror"
                                    placeholder="آدرس کامل مشتری..."
                                >{{ old('address', $customer->address) }}</textarea>
                            </div>
                            @error('address')
                                <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1 animate-shake">
                                    <i class="ti ti-alert-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center gap-4 pt-10 border-t border-slate-50">
                        <button type="submit" class="btn-modern btn-modern-warning w-full md:w-auto py-4 px-12 shadow-xl shadow-amber-500/20 group">
                            <span>ذخیره تغییرات مشتری</span>
                            <i class="ti ti-device-floppy group-hover:scale-125 transition-transform"></i>
                        </button>
                        <a href="{{ route('automation.customers.show', $customer) }}" class="btn-modern btn-modern-light w-full md:w-auto py-4 px-12">
                            <span>انصراف</span>
                        </a>
                    </div>
                </form>
            </x-enhanced-card>
        </div>
    </div>
</div>
@endsection
