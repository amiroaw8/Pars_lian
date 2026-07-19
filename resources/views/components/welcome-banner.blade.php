<?php
    $user = \Illuminate\Support\Facades\Auth::user();
    $hour = date('H');
    $greeting = 'به سامانه پارس لیان خوش آمدید';
    
    if ($hour < 12) {
        $timeGreeting = 'صبح بخیر';
    } elseif ($hour < 18) {
        $timeGreeting = 'ظهر بخیر';
    } else {
        $timeGreeting = 'عصر بخیر';
    }
?>

<div {{ $attributes->merge(['class' => 'relative mb-8 group']) }}>
    <div class="overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl border border-slate-700 shadow-xl shadow-slate-900/50 p-6 md:p-8 animate-fade-in relative">
        <!-- Background Decoration -->
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-primary-50 rounded-full blur-3xl opacity-50 group-hover:bg-primary-100 transition-colors duration-700"></div>
        <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-blue-50 rounded-full blur-3xl opacity-50 group-hover:bg-blue-100 transition-colors duration-700"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-primary-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-primary-500/20 animate-float">
                <i class="ti ti-confetti text-4xl"></i>
            </div>
            
            <div class="text-center md:text-right flex-1">
                <div class="flex flex-col md:flex-row md:items-center gap-2 mb-2">
                    <h3 class="text-2xl md:text-3xl font-black text-white">
                        {{ $timeGreeting }}، 
                        @if($user)
                            <span class="text-blue-200">{{ $user->name }}</span>
                        @else
                            <span class="text-blue-100">کاربر گرامی</span>
                        @endif
                    </h3>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-wider border border-emerald-100 animate-pulse mx-auto md:mx-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        سیستم فعال است
                    </div>
                </div>
                <p class="text-white/80 font-medium leading-relaxed">
                    {{ $greeting }}. امیدواریم تجربه کاربری فوق‌العاده‌ای در مدیریت خدمات فنی خود داشته باشید.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="hidden lg:flex flex-col items-end mr-4">
                    <span class="text-xs text-white/40 font-bold uppercase tracking-widest">تاریخ امروز</span>
                    <span class="text-sm font-black text-white">
                        @if(class_exists('\Morilog\Jalali\Jalalian'))
                            {{ \Morilog\Jalali\Jalalian::now()->format('%A، %d %B %Y') }}

                        @else
                            {{ date('Y/m/d') }}

                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
