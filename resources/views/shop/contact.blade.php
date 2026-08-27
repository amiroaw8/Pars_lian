@extends('layouts.shop')

@section('title', 'تماس با ما - پارس لیان')

@section('shop-content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-10 sm:p-14">
        <h1 class="text-3xl font-black text-gray-900 mb-6">تماس با ما</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3 text-green-600 mb-2">
                    <i class="ti ti-phone text-xl"></i>
                    <span class="font-bold">تلفن</span>
                </div>
                <p class="text-gray-800 font-black"><span dir="ltr" class="inline-block">{{ \App\Support\CompanyProfile::PHONE }}</span></p>
            </div>
            <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3 text-blue-600 mb-2">
                    <i class="ti ti-mail text-xl"></i>
                    <span class="font-bold">ایمیل</span>
                </div>
                <p class="text-gray-800 font-black">info@plian.ir</p>
            </div>
            <div class="sm:col-span-2 p-6 rounded-2xl bg-gray-50 border border-gray-100">
                <div class="flex items-center gap-3 text-red-500 mb-2">
                    <i class="ti ti-map-pin text-xl"></i>
                    <span class="font-bold">آدرس</span>
                </div>
                <p class="text-gray-700 leading-7">{{ \App\Support\CompanyProfile::ADDRESS }}</p>
            </div>
        </div>
        <p class="text-sm text-gray-500">ساعات پاسخگویی: شنبه تا پنج‌شنبه ۸:۳۰ تا ۱۲:۳۰</p>
    </div>
</div>
@endsection
