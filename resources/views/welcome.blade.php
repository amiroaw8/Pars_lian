<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خوش آمدید - پارس لیان</title>
    
    <!-- Fonts & Icons -->
    <link rel="preload" href="{{ asset('fonts/vazirmatn/fonts/webfonts/Vazirmatn[wght].woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('fonts/vazirmatn/Vazirmatn-Variable-font-face.css') }}" rel="stylesheet">
    @include('partials.tabler-icons-assets')
    @vite(['resources/css/app.css', 'resources/css/partials/form-enhancements.css', 'resources/js/app.js'])

    <style>
        body {
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .hero-gradient {
            background: radial-gradient(circle at 0% 0%, #eff6ff 0%, transparent 50%),
                        radial-gradient(circle at 100% 100%, #faf5ff 0%, transparent 50%);
        }

        .floating-blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 197, 253, 0.1) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            animation: blob-float 20s infinite alternate;
        }

        @keyframes blob-float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.1); }
        }

        .welcome-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        }

        .btn-welcome-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .btn-welcome-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4);
        }
    </style>
</head>
<body class="hero-gradient min-h-screen flex items-center justify-center p-4">
    <!-- Background Elements -->
    <div class="floating-blob top-[-10%] left-[-10%]"></div>
    <div class="floating-blob bottom-[-10%] right-[-10%]" style="animation-delay: -5s; background: linear-gradient(135deg, rgba(168, 85, 247, 0.1) 0%, rgba(216, 180, 254, 0.1) 100%);"></div>

    <div class="max-w-4xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10">
        <!-- Left Side: Welcome Content -->
        <div class="text-right space-y-8 animate-slide-up">
            <div class="inline-flex items-center gap-3 px-4 py-2 bg-blue-50 text-blue-600 rounded-full text-sm font-bold">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                نسخه جدید پلتفرم پارس لیان
            </div>
            
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 leading-tight">
                مدیریت هوشمند <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-l from-blue-600 to-indigo-600">خدمات و مشتریان</span>
            </h1>
            
            <p class="text-xl text-slate-600 leading-relaxed max-w-md">
                پلتفرم یکپارچه پارس لیان برای مدیریت بهینه سفارشات، انبارداری و ارتباط با مشتریان شما طراحی شده است.
            </p>

            <div class="flex flex-wrap gap-4">
                <a href="{{ route('login') }}" class="btn-welcome-primary px-8 py-4 rounded-2xl font-black text-lg flex items-center gap-3 transition-all duration-300">
                    <i class="ti ti-login text-xl"></i>
                    ورود به پنل کاربری
                </a>
                <a href="{{ route('register') }}" class="bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 px-8 py-4 rounded-2xl font-black text-lg flex items-center gap-3 transition-all duration-300 shadow-sm">
                    <i class="ti ti-user-plus text-xl text-slate-400"></i>
                    ثبت‌نام سریع
                </a>
            </div>

            <div class="pt-8 flex items-center gap-6">
                <div class="flex -space-x-3 rtl:space-x-reverse">
                    @for($i = 1; $i <= 4; $i++)
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-500">U{{ $i }}</div>
                    @endfor
                    <div class="w-10 h-10 rounded-full border-2 border-white bg-blue-600 flex items-center justify-center text-xs font-bold text-white">+{{ rand(50, 200) }}</div>
                </div>
                <div class="text-sm">
                    <span class="block font-bold text-slate-900">بیش از ۵۰۰ کاربر فعال</span>
                    <span class="text-slate-500">در حال استفاده از خدمات سیستم</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Visual Card -->
        <div class="hidden lg:block animate-fade-in" style="animation-delay: 0.3s;">
            <div class="welcome-card rounded-[3rem] p-10 relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-blue-200 group-hover:rotate-12 transition-transform duration-500">
                            <i class="ti ti-building-store"></i>
                        </div>
                        <div class="text-left">
                            <span class="block text-2xl font-black text-slate-900">Pars Lian</span>
                            <span class="text-sm text-slate-400 font-medium tracking-widest uppercase">Management System</span>
                        </div>
                    </div>

                    <div class="bg-slate-50/50 rounded-2xl p-6 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center"><i class="ti ti-check"></i></div>
                                <span class="text-sm font-bold text-slate-700">سفارش شماره #۱۲۴۰ تکمیل شد</span>
                            </div>
                            <span class="text-xs text-slate-400">۲ دقیقه پیش</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center"><i class="ti ti-plus"></i></div>
                                <span class="text-sm font-bold text-slate-700">مشتری جدید ثبت‌نام کرد</span>
                            </div>
                            <span class="text-xs text-slate-400">۱۰ دقیقه پیش</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white rounded-xl shadow-sm opacity-50">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center"><i class="ti ti-clock"></i></div>
                                <span class="text-sm font-bold text-slate-700">در انتظار بررسی قطعات...</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('shop.index') }}" class="flex items-center justify-center gap-2 p-4 bg-slate-900 text-white rounded-2xl hover:bg-slate-800 transition-colors">
                            <i class="ti ti-shopping-cart"></i>
                            فروشگاه
                        </a>
                        <a href="{{ route('shop.tracking') }}" class="flex items-center justify-center gap-2 p-4 bg-white text-slate-900 border border-slate-200 rounded-2xl hover:bg-slate-50 transition-colors">
                            <i class="ti ti-search"></i>
                            پیگیری سفارش
                        </a>
                    </div>
                </div>

                <!-- Decorative Circles -->
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-50 rounded-full -z-10"></div>
            </div>
        </div>
    </div>

    <!-- Footer Copyright -->
    <div class="absolute bottom-6 text-slate-400 text-sm font-medium">
        &copy; {{ date('Y') }} تمامی حقوق برای پلتفرم پارس لیان محفوظ است.
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Add any specific welcome page JS here
        });
    </script>
</body>
</html>
