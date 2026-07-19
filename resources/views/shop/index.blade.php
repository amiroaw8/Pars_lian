@extends('layouts.shop')

@section('title', 'فروشگاه پارس لیان - بهترین قطعات کامپیوتر')

@section('shop-content')
    <div class="bg-white min-h-screen font-vazir">
        <!-- Hero Section -->
        <div class="relative bg-slate-900 overflow-hidden pt-12 pb-16 lg:pt-16 lg:pb-24">
            <div class="absolute inset-0">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-900/60 via-slate-900 to-indigo-900/60 mix-blend-multiply">
                </div>
            </div>

            <!-- Animated Background Blobs -->
            <div
                class="absolute top-0 -left-4 w-96 h-96 bg-blue-500 rounded-full mix-blend-screen filter blur-3xl opacity-10 animate-blob">
            </div>
            <div
                class="absolute top-0 -right-4 w-96 h-96 bg-indigo-500 rounded-full mix-blend-screen filter blur-3xl opacity-10 animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute -bottom-8 left-20 w-96 h-96 bg-purple-500 rounded-full mix-blend-screen filter blur-3xl opacity-10 animate-blob animation-delay-4000">
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center lg:text-right flex flex-col lg:flex-row items-center justify-between gap-10">
                    <div class="lg:w-1/2 animate-fade-in">
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm font-bold mb-6">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                            </span>
                            مرکز تخصصی سخت‌افزار پارس لیان
                        </div>
                        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight">
                            <span class="text-transparent bg-clip-text bg-gradient-to-l from-blue-400 to-cyan-300">تکنولوژی</span>
                            به شایستگی شما
                        </h1>
                        <p class="text-xl text-slate-400 mb-10 leading-relaxed max-w-xl">
                            تأمین بهترین قطعات کامپیوتر با گارانتی معتبر و ارائه خدمات تخصصی تعمیرات با پیشرفته‌ترین تجهیزات
                            روز دنیا.
                        </p>
                        <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                            <a href="{{ route('tracking.index') }}"
                                class="btn-modern btn-primary py-4 px-10 rounded-2xl shadow-xl shadow-blue-500/20">
                                <i class="ti ti-search text-xl"></i>
                                رهگیری سریع سفارش
                            </a>
                            <a href="{{ route('catalog.index') }}"
                                class="px-10 py-4 rounded-2xl font-bold text-white bg-white/5 backdrop-blur-md border border-white/10 hover:bg-white/10 transition-all flex items-center gap-2 group">
                                <i class="ti ti-shopping-cart text-xl group-hover:scale-110 transition-transform"></i>
                                مشاهده محصولات
                            </a>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-8 grid grid-cols-3 gap-4 md:gap-6 border-t border-white/5 pt-6">
                            <div class="group cursor-default">
                                <div class="text-2xl md:text-3xl font-black text-white group-hover:text-blue-400 transition-colors">
                                    +۲۰۰۰۰</div>
                                <div class="text-[10px] md:text-xs text-slate-500 font-bold tracking-wide mt-1">تعمیرات موفق
                                </div>
                            </div>
                            <div class="group cursor-default">
                                <div class="text-2xl md:text-3xl font-black text-white group-hover:text-indigo-400 transition-colors">
                                    +۲۵</div>
                                <div class="text-[10px] md:text-xs text-slate-500 font-bold tracking-wide mt-1">سال سابقه کار
                                </div>
                            </div>
                            <div class="group cursor-default">
                                <div class="text-3xl font-black text-white group-hover:text-emerald-400 transition-colors">
                                    ۱۰۰٪</div>
                                <div class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">رضایت مشتری
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-1/2 relative animate-slide-up">
                        <div class="relative z-10 flex justify-center items-center">
                            <img loading="eager" fetchpriority="high"
                                src="{{ \App\Support\BrandLogo::heroUrl() }}"
                                alt="پارس لیان — Pars Lian"
                                oncontextmenu="return false;" ondragstart="return false;"
                                class="relative w-full h-auto max-w-[640px] rounded-2xl object-contain drop-shadow-2xl transition-transform duration-700 hover:scale-[1.02] select-none pointer-events-none">

                            <!-- Floating Badge -->
                            <div
                                class="absolute top-0 left-0 bg-white shadow-lg border border-slate-100 px-4 py-2 rounded-2xl hidden lg:block animate-float z-20">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-md">
                                        <i class="ti ti-shield-check text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-slate-900 font-bold text-xs">ضمانت اصالت</div>
                                        <div class="text-blue-600 font-bold text-[10px]">گارانتی پارس لیان</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 lg:mt-14 relative">
            <div
                class="bg-white/90 backdrop-blur-2xl rounded-[3rem] border border-white/50 shadow-2xl shadow-slate-200/50 overflow-hidden">
                <div
                    class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-slate-100">
                    <div class="flex items-center p-10 group hover:bg-blue-50/50 transition-all duration-500">
                        <div
                            class="w-16 h-16 bg-blue-100/50 text-blue-600 rounded-[1.5rem] flex items-center justify-center ml-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-sm border border-blue-200/50">
                            <i class="ti ti-cpu text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900">تعمیرات تخصصی</h3>
                            <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">با پیشرفته‌ترین تجهیزات روز دنیا</p>
                        </div>
                    </div>
                    <div class="flex items-center p-10 group hover:bg-indigo-50/50 transition-all duration-500">
                        <div
                            class="w-16 h-16 bg-indigo-100/50 text-indigo-600 rounded-[1.5rem] flex items-center justify-center ml-6 group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500 shadow-sm border border-indigo-200/50">
                            <i class="ti ti-components text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900">قطعات اورجینال</h3>
                            <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">تضمین ۱۰۰٪ اصالت و کیفیت</p>
                        </div>
                    </div>
                    <div class="flex items-center p-10 group hover:bg-emerald-50/50 transition-all duration-500">
                        <div
                            class="w-16 h-16 bg-emerald-100/50 text-emerald-600 rounded-[1.5rem] flex items-center justify-center ml-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-sm border border-emerald-200/50">
                            <i class="ti ti-rocket text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900">تحویل اکسپرس</h3>
                            <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">ارسال فوری در کمترین زمان</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured / Products sections continue below -->

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <!-- Featured Products -->
            @if($featuredProducts->count() > 0)
                <section class="mb-24">
                    <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
                        <div class="animate-fade-in">
                            <h2 class="text-4xl font-black text-slate-900 mb-4 flex items-center gap-3">
                                <span class="w-12 h-1.5 bg-blue-600 rounded-full"></span>
                                پیشنهادات ویژه
                            </h2>
                            <p class="text-slate-500 text-lg mr-15">مجموعه‌ای از بهترین و با کیفیت‌ترین قطعات منتخب ما برای شما
                            </p>
                        </div>
                        <a href="{{ route('catalog.index') }}"
                            class="btn-modern bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white px-8 py-3 rounded-2xl group">
                            مشاهده همه محصولات
                            <i class="ti ti-arrow-left text-lg group-hover:-translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <?php $__currentLoopData = $featuredProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="group bg-white rounded-[2.5rem] border border-slate-100 p-4 hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 animate-slide-up"
                                style="animation-delay: <?php        echo $loop->index * 0.1; ?>s">
                                <div class="relative overflow-hidden rounded-[2rem] pt-[100%] bg-slate-50">
                                    <img loading="lazy" src="{{ $product->main_image_url }}" alt="{{ $product->name }}"
                                         onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';"
                                        class="absolute inset-0 z-[1] w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-700">

                                    @if($product->is_on_sale)
                                        <div
                                            class="absolute top-4 right-4 bg-rose-500 text-white px-4 py-1.5 rounded-full text-xs font-black shadow-lg shadow-rose-500/30 z-10">
                                            فروش ویژه
                                        </div>
                                    @endif

                                    <div
                                        class="absolute inset-0 bg-blue-900/40 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-center justify-center gap-4 z-20">
                                        <button type="button"
                                            class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-slate-900 hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-xl transform translate-y-8 group-hover:translate-y-0 transition-all delay-75 btn-add-to-cart"
                                            data-product-slug="{{ $product->slug }}">
                                            <i class="ti ti-shopping-cart text-2xl"></i>
                                        </button>
                                        <a href="{{ route('shop.show', $product) }}"
                                            class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-slate-900 hover:bg-blue-600 hover:text-white transition-all duration-300 shadow-xl transform translate-y-8 group-hover:translate-y-0 transition-all delay-150">
                                            <i class="ti ti-eye text-2xl"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="p-6">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span
                                            class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md uppercase tracking-wider">
                                            {{ $product->category->name ?? 'سخت‌افزار' }}

                                        </span>
                                    </div>
                                    <h3
                                        class="font-black text-slate-900 mb-3 line-clamp-2 min-h-[3rem] leading-snug group-hover:text-blue-600 transition-colors">
                                        <a href="{{ route('shop.show', $product) }}">{{ $product->name }}</a>
                                    </h3>
                                    <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-50">
                                        <div class="flex flex-col">
                                            @if($product->is_on_sale)
                                                <span
                                                    class="text-xs text-slate-400 line-through mb-0.5">{{ number_format($product->price) }}</span>
                                                <span
                                                    class="text-xl font-black text-blue-600 tracking-tight">{{ number_format($product->sale_price) }}

                                                    <small class="text-xs font-bold text-slate-400 mr-1">تومان</small></span>
                                            @else
                                                <span
                                                    class="text-xl font-black text-slate-900 tracking-tight">{{ number_format($product->price) }}

                                                    <small class="text-xs font-bold text-slate-400 mr-1">تومان</small></span>
                                            @endif
                                        </div>
                                        <button type="button"
                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all btn-add-to-cart"
                                            data-product-slug="{{ $product->slug }}">
                                            <i class="ti ti-plus text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            @endif

            <!-- Why Us Section (Trust Signals) -->
            <div class="bg-slate-50 py-24 relative overflow-hidden">
                <div
                    class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent">
                </div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div class="animate-fade-in">
                            <h2 class="text-4xl font-black text-slate-900 mb-8 leading-tight">
                                چرا متخصصین سخت‌افزار <br>
                                <span class="text-blue-600">پارس لیان</span> را انتخاب می‌کنند؟
                            </h2>
                            <p class="text-slate-500 text-base md:text-lg mb-10 leading-relaxed">
                                ما در پارس لیان فقط فروشنده نیستیم؛ ما شریک تکنولوژی شما هستیم در فروش، تعمیرات و خدمات پس از فروش. تعهد ما به کیفیت و تخصص، تضمین‌کننده عملکرد بی‌نقص سیستم‌های شما در تمام مراحل – از تأمین قطعه تا پشتیبانی و تعمیر – خواهد بود.
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                <div class="flex gap-4">
                                    <div
                                        class="flex-shrink-0 w-12 h-12 bg-white shadow-sm rounded-xl flex items-center justify-center text-blue-600 border border-slate-100">
                                        <i class="ti ti-certificate text-2xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-900 mb-1">اصالت تضمین شده</h4>
                                        <p class="text-sm text-slate-500">تمامی قطعات با سریال اصلی و گارانتی معتبر ارائه
                                            می‌شوند.</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div
                                        class="flex-shrink-0 w-12 h-12 bg-white shadow-sm rounded-xl flex items-center justify-center text-indigo-600 border border-slate-100">
                                        <i class="ti ti-tools text-2xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-900 mb-1">تیم فنی مجرب</h4>
                                        <p class="text-sm text-slate-500">بهره‌مندی از دانش بهترین تکنسین‌های سخت‌افزار
                                            ایران.</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div
                                        class="flex-shrink-0 w-12 h-12 bg-white shadow-sm rounded-xl flex items-center justify-center text-emerald-600 border border-slate-100">
                                        <i class="ti ti-truck text-2xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-900 mb-1">ارسال ایمن و سریع</h4>
                                        <p class="text-sm text-slate-500">بسته‌بندی تخصصی و ضدضربه برای تمامی قطعات حساس.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div
                                        class="flex-shrink-0 w-12 h-12 bg-white shadow-sm rounded-xl flex items-center justify-center text-rose-600 border border-slate-100">
                                        <i class="ti ti-headset text-2xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-slate-900 mb-1">پشتیبانی دائمی</h4>
                                        <p class="text-sm text-slate-500">همراهی شما از لحظه خرید تا نصب و راه‌اندازی قطعه.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative lg:ml-8">
                            <div class="absolute -inset-4 bg-blue-600/10 rounded-[3rem] blur-2xl transform rotate-3"></div>
                            <div
                                class="relative bg-white p-10 rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden">
                                <div class="absolute top-0 right-0 p-8 opacity-5">
                                    <i class="ti ti-quote text-9xl"></i>
                                </div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-4 mb-8">
                                        <div
                                            class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                                            <i class="ti ti-shield-check text-3xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-black text-slate-900">اعتماد شما، سرمایه ماست</h4>
                                            <p class="text-sm text-blue-600 font-bold">+۲۵ سال سابقه درخشان</p>
                                        </div>
                                    </div>
                                    <blockquote class="text-slate-600 leading-relaxed italic text-lg mb-8">
                                        "هدف ما در پارس لیان، ایجاد بستری امن برای تامین قطعاتی است که قلب تپنده سیستم‌های
                                        شما هستند. کیفیت هرگز اتفاقی نیست."
                                    </blockquote>
                                    <div class="flex items-center gap-4 border-t border-slate-100 pt-8">
                                        <div
                                            class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-400">
                                            <i class="ti ti-user text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900">مدیریت پارس لیان</p>
                                            <p class="text-xs text-slate-400 uppercase tracking-widest">Pars Lian Group</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent">
                </div>
            </div>



            <!-- Trust Badges Section -->
            <section class="py-24 border-t border-slate-100 mt-12">
                <div class="text-center mb-16 animate-fade-in">
                    <h2 class="text-4xl font-black text-slate-900 mb-4">اطمینان در خرید، تخصص در خدمات</h2>
                    <p class="text-slate-500 text-lg max-w-2xl mx-auto">ما در پارس لیان متعهد هستیم که بهترین تجربه را برای
                        شما رقم بزنیم.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12">
                    <div class="group text-center">
                        <div
                            class="w-24 h-24 bg-blue-50 text-blue-600 rounded-[2rem] flex items-center justify-center mx-auto mb-8 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 group-hover:-translate-y-2 shadow-sm border border-blue-100">
                            <i class="ti ti-certificate text-4xl"></i>
                        </div>
                        <h4 class="text-xl font-black text-slate-900 mb-3">ضمانت ۱۰۰٪ اصالت</h4>
                        <p class="text-sm text-slate-500 leading-relaxed px-4">تمامی قطعات ارائه شده با تضمین اصالت و سلامت
                            کامل فیزیکی عرضه می‌شوند.</p>
                    </div>
                    <div class="group text-center">
                        <div
                            class="w-24 h-24 bg-indigo-50 text-indigo-600 rounded-[2rem] flex items-center justify-center mx-auto mb-8 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500 group-hover:-translate-y-2 shadow-sm border border-indigo-100">
                            <i class="ti ti-headset text-4xl"></i>
                        </div>
                        <h4 class="text-xl font-black text-slate-900 mb-3">پشتیبانی تخصصی</h4>
                        <p class="text-sm text-slate-500 leading-relaxed px-4">مشاوره رایگان پیش از خرید توسط کارشناسان مجرب
                            سخت‌افزار پارس لیان.</p>
                    </div>
                    <div class="group text-center">
                        <div
                            class="w-24 h-24 bg-emerald-50 text-emerald-600 rounded-[2rem] flex items-center justify-center mx-auto mb-8 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 group-hover:-translate-y-2 shadow-sm border border-emerald-100">
                            <i class="ti ti-shield-check text-4xl"></i>
                        </div>
                        <h4 class="text-xl font-black text-slate-900 mb-3">گارانتی معتبر</h4>
                        <p class="text-sm text-slate-500 leading-relaxed px-4">ارائه خدمات پس از فروش و گارانتی طلایی برای
                            تمامی محصولات فروشگاه.</p>
                    </div>
                    <div class="group text-center">
                        <div
                            class="w-24 h-24 bg-rose-50 text-rose-600 rounded-[2rem] flex items-center justify-center mx-auto mb-8 group-hover:bg-rose-600 group-hover:text-white transition-all duration-500 group-hover:-translate-y-2 shadow-sm border border-rose-100">
                            <i class="ti ti-truck text-4xl"></i>
                        </div>
                        <h4 class="text-xl font-black text-slate-900 mb-3">ارسال فوق سریع</h4>
                        <p class="text-sm text-slate-500 leading-relaxed px-4">تحویل در سریع‌ترین زمان ممکن به تمام نقاط
                            کشور با بسته‌بندی ایمن.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <style>
        .font-vazir {
            font-family: 'Vazirmatn', Tahoma, sans-serif;
        }

        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .animate-slide-up {
            animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sort select logic
            const sortSelect = document.getElementById('sort-select');
            if (sortSelect) {
                sortSelect.addEventListener('change', function () {
                    this.form.submit();
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>
