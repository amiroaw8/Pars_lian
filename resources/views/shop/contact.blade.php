@extends('layouts.shop')

@section('title', 'تماس با ما - فروشگاه و تعمیرات تخصصی پارس لیان')
@section('meta_description', 'اطلاعات تماس، آدرس دقیق حضوری، شماره‌های تماس و راه‌های ارتباطی با فروشگاه و مرکز خدمات تخصصی کامپیوتر پارس لیان در خرم‌آباد.')
@section('canonical', route('shop.contact'))

@push('meta')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "ContactPage",
  "name": "تماس با فروشگاه پارس لیان",
  "description": "راه‌های ارتباطی، آدرس، تلفن و ساعات کاری پارس لیان",
  "url": "{{ route('shop.contact') }}",
  "mainEntity": {
    "@@type": "LocalBusiness",
    "name": "فروشگاه و تعمیرات کامپیوتر پارس لیان",
    "telephone": "{{ \App\Support\CompanyProfile::PHONE }}",
    "email": "info@plian.ir",
    "address": {
      "@@type": "PostalAddress",
      "streetAddress": "{{ \App\Support\CompanyProfile::ADDRESS }}",
      "addressLocality": "خرم‌آباد",
      "addressRegion": "لرستان",
      "addressCountry": "IR"
    },
    "openingHoursSpecification": [
      {
        "@@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Saturday", "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday"],
        "opens": "08:30",
        "closes": "12:30"
      },
      {
        "@@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Saturday", "Sunday", "Monday", "Tuesday", "Wednesday"],
        "opens": "16:30",
        "closes": "20:30"
      }
    ]
  }
}
</script>
@endpush

@section('shop-content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16" dir="rtl">
    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto mb-14">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 text-blue-600 text-xs font-bold mb-4">
            <i class="ti ti-headset text-sm"></i>
            همیشه پاسخگوی شما هستیم
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-gray-900 mb-4 tracking-tight">تماس با فروشگاه پارس لیان</h1>
        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
            برای مشاوره تخصصی خرید، پیگیری سفارشات و هماهنگی خدمات فنی و گارانتی می‌توانید از طریق راه‌های ارتباطی زیر با ما در ارتباط باشید.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Phone Info -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-6">
                <i class="ti ti-phone-call text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-gray-900 mb-2">تماس تلفنی و مشاوره</h3>
            <p class="text-gray-500 text-xs mb-4">پاسخگویی در ساعات کاری فروشگاه</p>
            <a href="tel:{{ \App\Support\CompanyProfile::PHONE }}" class="text-xl font-black text-gray-900 hover:text-blue-600 transition-colors block">
                <span dir="ltr">{{ \App\Support\CompanyProfile::PHONE }}</span>
            </a>
        </div>

        <!-- Email Info -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6">
                <i class="ti ti-mail text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-gray-900 mb-2">پشتیبانی ایمیلی</h3>
            <p class="text-gray-500 text-xs mb-4">ارسال پیشنهادات و درخواست‌های سازمانی</p>
            <a href="mailto:info@plian.ir" class="text-lg font-black text-gray-900 hover:text-blue-600 transition-colors block">
                info@plian.ir
            </a>
        </div>

        <!-- Working Hours -->
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-6">
                <i class="ti ti-clock text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-gray-900 mb-2">ساعات کاری حضوری</h3>
            <p class="text-gray-700 text-sm font-bold mb-1">شنبه تا پنج‌شنبه: ۸:۳۰ الی ۱۲:۳۰</p>
            <p class="text-gray-700 text-sm font-bold">عصرها: ۱۶:۳۰ الی ۲۰:۳۰</p>
            <p class="text-gray-400 text-xs mt-2">روزهای جمعه و تعطیلات رسمی تعطیل است.</p>
        </div>
    </div>

    <!-- Address Card -->
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-gray-100 shadow-sm mb-12">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i class="ti ti-map-pin text-3xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-black text-gray-900 mb-2">آدرس فروشگاه و مرکز خدمات</h3>
                <p class="text-gray-700 text-base leading-relaxed">{{ \App\Support\CompanyProfile::ADDRESS }}</p>
            </div>
            <div class="shrink-0 w-full sm:w-auto">
                <a href="{{ route('tracking.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-blue-600 text-white rounded-2xl font-bold text-sm hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                    <i class="ti ti-search"></i>
                    پیگیری آنلاین سفارش و تعمیرات
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
