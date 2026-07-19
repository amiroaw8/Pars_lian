@extends('layouts.admin')

@section('title', 'ایجاد مشتری جدید - پارس لیان')

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
                        <i class="ti ti-user-plus text-amber-400"></i>
                        افزودن به سیستم
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">ایجاد مشتری جدید</h2>
                    <p class="text-blue-100 text-lg font-medium max-w-xl leading-relaxed">با ثبت اطلاعات مشتری، می‌توانید سفارشات تعمیرات و خدمات را به صورت یکپارچه مدیریت کنید.</p>
                </div>
                <div class="flex flex-shrink-0">
                    <div class="w-24 h-24 md:w-40 md:h-40 bg-white/20 backdrop-blur-xl rounded-[2.5rem] flex items-center justify-center text-white border border-white/30 shadow-2xl animate-float group-hover:scale-110 transition-transform duration-500">
                        <i class="ti ti-user-plus text-6xl md:text-8xl drop-shadow-lg"></i>
                    </div>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/20 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-400/20 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-blue-400/30 transition-colors duration-700"></div>
        </div>

        <div class="max-w-4xl mx-auto">
            <x-enhanced-card title="اطلاعات هویتی و تماس" icon="user-edit" class="animate-slide-up">
                <x-slot name="actions">
                    <a href="{{ route('automation.customers.index') }}" class="btn-modern btn-modern-light py-2 px-6 text-sm group">
                        <i class="ti ti-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        <span>بازگشت به لیست</span>
                    </a>
                </x-slot>

                <form method="POST" action="{{ route('automation.customers.store') }}" class="space-y-10">
                    @csrf
                    <input type="hidden" name="in_person" value="1">

                    @if(request()->has('return_to'))
                        <input type="hidden" name="return_to" value="{{ request('return_to') }}">
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="form-group-modern group">
                            <label for="name" class="form-label-modern group-focus-within:text-blue-600 transition-colors">
                                <i class="ti ti-user text-lg"></i>
                                نام و نام خانوادگی مشتری
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="name"
                                    name="name" 
                                    class="form-control-modern @error('name') border-rose-500 @enderror"
                                    placeholder="مثلاً: علی محمدی"
                                    value="{{ old('name') }}"
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
                            <label for="phone" class="form-label-modern group-focus-within:text-blue-600 transition-colors">
                                <i class="ti ti-phone text-lg"></i>
                                شماره همراه
                            </label>
                            <div class="relative">
                                <input 
                                    type="tel" 
                                    id="phone"
                                    name="phone" 
                                    class="form-control-modern text-left dir-ltr font-black tracking-widest @error('phone') border-rose-500 @enderror"
                                    placeholder="09123456789"
                                    value="{{ old('phone') }}"
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

                        <div class="form-group-modern md:col-span-2 group">
                            <label for="address" class="form-label-modern group-focus-within:text-blue-600 transition-colors">
                                <i class="ti ti-map-pin text-lg"></i>
                                آدرس دقیق محل سکونت / شرکت
                            </label>
                            <div class="relative">
                                <textarea 
                                    id="address"
                                    name="address" 
                                    class="form-control-modern min-h-[120px] py-4 @error('address') border-rose-500 @enderror"
                                    placeholder="آدرس کامل مشتری را جهت مراجعات بعدی وارد کنید..."
                                >{{ old('address') }}</textarea>
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
                        <button type="submit" class="btn-modern btn-modern-primary w-full md:w-auto py-4 px-12 shadow-xl shadow-blue-500/20 group">
                            <span>ثبت و ذخیره مشتری</span>
                            <i class="ti ti-check group-hover:scale-125 transition-transform"></i>
                        </button>
                        <a href="{{ route('automation.customers.index') }}" class="btn-modern btn-modern-light w-full md:w-auto py-4 px-12">
                            <span>انصراف</span>
                        </a>
                    </div>
                </form>
            </x-enhanced-card>
        </div>

        <!-- Help Section -->
        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
                <div class="relative z-10 flex items-start gap-6">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-sm">
                        <i class="ti ti-info-circle text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-slate-900 mb-2">چرا ثبت مشتری؟</h4>
                        <p class="text-slate-500 text-sm font-medium leading-relaxed">ثبت اطلاعات دقیق مشتری به شما کمک می‌کند تا سوابق تعمیراتی هر شخص را به تفکیک داشته باشید و در مراجعات بعدی، خدمات سریع‌تری ارائه دهید.</p>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
                <div class="relative z-10 flex items-start gap-6">
                    <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all duration-500 shadow-sm">
                        <i class="ti ti-shield-check text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-slate-900 mb-2">حریم خصوصی</h4>
                        <p class="text-slate-500 text-sm font-medium leading-relaxed">تمامی اطلاعات مشتریان در سامانه پارس لیان به صورت رمزنگاری شده ذخیره شده و تنها برای استفاده در فرآیندهای اداری و اطلاع‌رسانی سفارشات می‌باشد.</p>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-amber-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
        </div>
    </div>
</div>
@endsection
