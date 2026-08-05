@extends('layouts.app')
@section('use-alpine', true)

@section('title', 'ویرایش پروفایل کاربری')
@section('page_title', 'تنظیمات حساب کاربری')

@section('content')
<div class="relative">
    <!-- background decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/2 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/4 w-64 h-64 bg-slate-500/5 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
    </div>

    <div class="max-w-6xl mx-auto space-y-8 relative z-10">
        <!-- Welcome Modal for New Registration -->
        @if(session('new_registration') || request()->has('new'))
        <div x-data="{ show: true }" x-show="show" class="animate-fade-in">
            <div class="bg-gradient-to-br from-amber-500 via-orange-600 to-rose-700 rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-orange-500/30 relative overflow-hidden group mb-8">
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                    <div class="w-24 h-24 md:w-32 md:h-32 bg-white/20 backdrop-blur-xl rounded-[2.5rem] flex items-center justify-center text-white border border-white/30 shadow-2xl shrink-0 animate-bounce">
                        <i class="ti ti-gift text-5xl md:text-6xl drop-shadow-lg"></i>
                    </div>
                    <div class="text-center md:text-right flex-1">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-4 border border-white/20 uppercase tracking-widest">
                            <i class="ti ti-sparkles text-yellow-300"></i>
                            خوش آمدید، کاربر جدید!
                        </div>
                        <h2 class="text-2xl md:text-4xl font-black text-white mb-4 leading-tight">خوش آمدید! 🚀</h2>
                        <p class="text-orange-50 text-lg font-medium leading-relaxed max-w-2xl">
                            تبریک می‌گوییم! حساب شما با موفقیت ایجاد شد. برای تجربه بهتر و امکان ثبت سفارش سریع‌تر، پیشنهاد می‌کنیم اطلاعات پروفایل خود را تکمیل کنید.
                        </p>
                    </div>
                    <button @click="show = false" class="absolute top-6 left-6 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white border border-white/20 transition-all">
                        <i class="ti ti-x text-xl"></i>
                    </button>
                </div>
                
                <!-- Background Decorative Elements -->
                <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-yellow-400/20 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl"></div>
            </div>
        </div>
        @endif

        <!-- Header Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-black rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-slate-900/20 animate-fade-in group">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-right">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white/90 text-xs font-black mb-6 border border-white/20 uppercase tracking-widest">
                        <i class="ti ti-settings text-blue-400"></i>
                        تنظیمات حساب
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">پروفایل کاربری</h2>
                    <p class="text-slate-300 text-lg font-medium max-w-xl leading-relaxed">در این بخش می‌توانید اطلاعات شخصی، آدرس و رمز عبور خود را مدیریت و به‌روزرسانی کنید.</p>
                </div>
                <div class="flex flex-shrink-0">
                    <div class="w-24 h-24 md:w-40 md:h-40 bg-white/10 backdrop-blur-xl rounded-[2.5rem] flex items-center justify-center text-white border border-white/20 shadow-2xl animate-float group-hover:scale-110 transition-transform duration-500">
                        <i class="ti ti-user-edit text-6xl md:text-8xl drop-shadow-lg"></i>
                    </div>
                </div>
            </div>
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl group-hover:bg-blue-500/20 transition-colors duration-700"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar Info -->
            <div class="space-y-8">
                <div class="animate-slide-up">
                    <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm text-center relative overflow-hidden group hover:shadow-xl transition-all duration-500">
                        <div class="relative z-10">
                            <div class="w-28 h-28 mx-auto bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-600 rounded-[2.5rem] flex items-center justify-center text-4xl font-black mb-6 shadow-inner group-hover:scale-110 transition-transform duration-500 border border-blue-100/50">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-2">{{ $user->name }}</h3>
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 text-slate-500 text-xs font-black mb-8 border border-slate-100">
                                <i class="ti ti-phone text-blue-500"></i>
                                {{ $user->phone }}
                            </div>
                            
                            <div class="grid grid-cols-1 gap-4">
                                <div class="bg-slate-50/80 p-5 rounded-3xl border border-slate-100/50 hover:bg-white hover:shadow-md transition-all duration-300">
                                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">عضویت از</div>
                                    <div class="text-sm font-black text-slate-700 flex items-center justify-center gap-2">
                                        <i class="ti ti-calendar-event text-blue-500"></i>
                                        @if(class_exists('\Morilog\Jalali\Jalalian'))
                                            {{ \Morilog\Jalali\Jalalian::fromCarbon($user->created_at)->format('Y/m/d') }}
                                        @else
                                            {{ $user->created_at->format('Y/m/d') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="bg-slate-50/80 p-5 rounded-3xl border border-slate-100/50 hover:bg-white hover:shadow-md transition-all duration-300">
                                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">نوع حساب</div>
                                    <div class="text-sm font-black text-slate-700 flex items-center justify-center gap-2">
                                        <i class="ti ti-shield-check text-indigo-500"></i>
                                        {{ $user->getRoleDisplayName() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-blue-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                </div>

                <div class="animate-slide-up" style="animation-delay: 0.1s">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-xl shadow-blue-500/20 group">
                        <div class="relative z-10">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-6 border border-white/30 group-hover:scale-110 transition-transform">
                                <i class="ti ti-shield-lock text-3xl"></i>
                            </div>
                            <h4 class="text-xl font-black mb-4">امنیت حساب کاربری</h4>
                            <p class="text-blue-100 text-sm font-medium leading-relaxed mb-6 opacity-80">برای محافظت از اطلاعات و سفارشات خود، پیشنهاد می‌شود هر چند وقت یکبار رمز عبور خود را تغییر دهید.</p>
                        </div>
                        <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="lg:col-span-2 space-y-8">
                <x-enhanced-card title="ویرایش اطلاعات شخصی" icon="user-edit" class="animate-slide-up" style="animation-delay: 0.2s">
                    <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-10">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="form-group-modern group">
                                <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">نام</label>
                                <div class="relative">
                                    <i class="ti ti-user absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                    <input type="text" name="first_name" class="form-control-modern pr-12 @error('first_name') border-rose-500 @enderror" value="{{ old('first_name', $user->first_name) }}" required placeholder="نام خود را وارد کنید">
                                </div>
                                @error('first_name') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">نام خانوادگی</label>
                                <div class="relative">
                                    <i class="ti ti-user absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                    <input type="text" name="last_name" class="form-control-modern pr-12 @error('last_name') border-rose-500 @enderror" value="{{ old('last_name', $user->last_name) }}" required placeholder="نام خانوادگی خود را وارد کنید">
                                </div>
                                @error('last_name') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <div class="form-group-modern group">
                                <label class="form-label-modern">شماره همراه (نام کاربری)</label>
                                <div class="relative">
                                    <i class="ti ti-phone absolute right-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                    <input type="text" class="form-control-modern pr-12 bg-slate-50 text-slate-400 cursor-not-allowed border-slate-100" value="{{ $user->phone }}" disabled>
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2">
                                        <i class="ti ti-lock text-slate-300 text-sm"></i>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-2 font-black uppercase tracking-widest">شماره همراه قابل تغییر نمی‌باشد</p>
                            </div>

                            <div class="form-group-modern group">
                                <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">آدرس ایمیل (اختیاری)</label>
                                <div class="relative">
                                    <i class="ti ti-mail absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                    <input type="email" name="email" class="form-control-modern pr-12 @error('email') border-rose-500 @enderror" value="{{ old('email', $user->email) }}" placeholder="example@mail.com">
                                </div>
                                @error('email') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- آدرس و مشخصات پستی -->
                        <div class="pt-10 border-t border-slate-100 space-y-8">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                                    <i class="ti ti-map-pin text-xl"></i>
                                </div>
                                <h4 class="text-lg font-black text-slate-900">اطلاعات آدرس و ارسال</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="form-group-modern group">
                                    <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">استان</label>
                                    <div class="relative">
                                        <i class="ti ti-map absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <input type="text" name="province" class="form-control-modern pr-12 @error('province') border-rose-500 @enderror" value="{{ old('province', $user->province) }}" placeholder="مثلاً: تهران">
                                    </div>
                                    @error('province') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                                </div>

                                <div class="form-group-modern group">
                                    <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">شهر</label>
                                    <div class="relative">
                                        <i class="ti ti-building-community absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <input type="text" name="city" class="form-control-modern pr-12 @error('city') border-rose-500 @enderror" value="{{ old('city', $user->city) }}" placeholder="مثلاً: تهران">
                                    </div>
                                    @error('city') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                                </div>

                                <div class="form-group-modern group">
                                    <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">خیابان</label>
                                    <div class="relative">
                                        <i class="ti ti-road absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <input type="text" name="street" class="form-control-modern pr-12 @error('street') border-rose-500 @enderror" value="{{ old('street', $user->street) }}" placeholder="نام خیابان اصلی">
                                    </div>
                                    @error('street') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                                </div>

                                <div class="form-group-modern group">
                                    <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">کوچه / فرعی</label>
                                    <div class="relative">
                                        <i class="ti ti-directions absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <input type="text" name="alley" class="form-control-modern pr-12 @error('alley') border-rose-500 @enderror" value="{{ old('alley', $user->alley) }}" placeholder="نام کوچه یا خیابان فرعی">
                                    </div>
                                    @error('alley') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                                </div>

                                <div class="form-group-modern group">
                                    <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">پلاک</label>
                                    <div class="relative">
                                        <i class="ti ti-hash absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <input type="text" name="plate" class="form-control-modern pr-12 @error('plate') border-rose-500 @enderror" value="{{ old('plate', $user->plate) }}" placeholder="شماره پلاک واحد">
                                    </div>
                                    @error('plate') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                                </div>

                                <div class="form-group-modern group">
                                    <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">کد پستی</label>
                                    <div class="relative">
                                        <i class="ti ti-mailbox absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <input type="text" name="postal_code" class="form-control-modern pr-12 @error('postal_code') border-rose-500 @enderror" value="{{ old('postal_code', $user->postal_code) }}" placeholder="کد پستی ۱۰ رقمی">
                                    </div>
                                    @error('postal_code') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-10 border-t border-slate-100 space-y-8">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                                    <i class="ti ti-key text-xl"></i>
                                </div>
                                <h4 class="text-lg font-black text-slate-900">تغییر رمز عبور</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="form-group-modern group">
                                    <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">رمز عبور جدید</label>
                                    <div class="relative">
                                        <i class="ti ti-lock absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <input type="password" name="password" class="form-control-modern pr-12 @error('password') border-rose-500 @enderror" placeholder="••••••••">
                                    </div>
                                    @error('password') <p class="text-rose-500 text-xs mt-2 font-black flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p> @enderror
                                </div>

                                <div class="form-group-modern group">
                                    <label class="form-label-modern group-focus-within:text-blue-600 transition-colors">تکرار رمز عبور جدید</label>
                                    <div class="relative">
                                        <i class="ti ti-lock-check absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                        <input type="password" name="password_confirmation" class="form-control-modern pr-12" placeholder="••••••••">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row items-center gap-4 pt-10">
                            <button type="submit" class="btn-modern btn-modern-primary w-full md:w-auto py-4 px-12 shadow-xl shadow-blue-500/20 group">
                                <span>ذخیره تغییرات نهایی</span>
                                <i class="ti ti-check group-hover:scale-125 transition-transform"></i>
                            </button>
                            <a href="{{ route('customer.dashboard') }}" class="btn-modern btn-modern-light w-full md:w-auto py-4 px-12">
                                <span>انصراف و بازگشت</span>
                            </a>
                        </div>
                    </form>
                </x-enhanced-card>

                <!-- Active Sessions Section -->
                <div class="mt-8 animate-slide-up animation-delay-500">
                    <x-enhanced-card title="نشست‌های فعال" icon="ti-device-laptop" animated>
                        <div class="p-4">
                            <p class="text-slate-500 text-sm mb-6">در این بخش می‌توانید لیست تمامی دستگاه‌هایی که با حساب شما وارد شده‌اند را مشاهده و در صورت نیاز آن‌ها را ببندید. (حداکثر ۲ نشست مجاز است)</p>
                            
                            <div class="space-y-4">
                                @foreach($sessions as $session)
                                <div class="flex flex-col md:flex-row items-center justify-between p-6 rounded-3xl border {{ $session->is_current ? 'border-blue-200 bg-blue-50/30' : 'border-slate-100 bg-white' }} hover:shadow-lg transition-all duration-300 gap-6">
                                    <div class="flex items-center gap-6 flex-1 w-full">
                                        <div class="w-14 h-14 rounded-2xl {{ $session->is_current ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-2xl shadow-lg">
                                            @if(Str::contains(strtolower($session->user_agent), ['iphone', 'android', 'mobile']))
                                                <i class="ti ti-device-mobile"></i>
                                            @else
                                                <i class="ti ti-device-laptop"></i>
                                            @endif
                                        </div>
                                        <div class="flex flex-col gap-1 min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-black text-slate-800 truncate">{{ $session->ip_address }}</span>
                                                @if($session->is_current)
                                                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-wider border border-blue-200">این دستگاه</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-slate-400 truncate font-mono">{{ $session->user_agent }}</div>
                                            <div class="text-[10px] font-bold text-slate-500 flex items-center gap-1">
                                                <i class="ti ti-clock text-blue-400"></i>
                                                آخرین فعالیت: {{ $session->last_activity }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if(!$session->is_current)
                                    <form action="{{ route('auth.sessions.destroy', $session->id) }}" method="POST" class="w-full md:w-auto">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-modern btn-modern-danger w-full md:w-auto py-2.5 px-6 text-xs group" onclick="return confirm('آیا از بستن این نشست اطمینان دارید؟')">
                                            <span>بستن این نشست</span>
                                            <i class="ti ti-logout-2 group-hover:translate-x-1 transition-transform"></i>
                                        </button>
                                    </form>
                                    @else
                                    <div class="text-xs font-black text-blue-600 px-6 py-2.5 bg-blue-100/50 rounded-xl border border-blue-200/50">
                                        نشست فعلی شما
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </x-enhanced-card>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
