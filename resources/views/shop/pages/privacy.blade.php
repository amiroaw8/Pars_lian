@extends('layouts.shop')

@section('title', $title)

@push('meta')
<meta name="description" content="{{ $metaDesc }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $metaDesc }}">
@endpush

@section('shop-content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-10 sm:p-14">
        <h1 class="text-3xl font-black text-gray-900 mb-6">حریم خصوصی</h1>
        <div class="prose prose-slate max-w-none text-gray-600 leading-8 space-y-4">
            {!! $content !!}
        </div>
        <div class="mt-10">
            <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-colors">
                مشاهده محصولات
                <i class="ti ti-arrow-left"></i>
            </a>
        </div>
    </div>
</div>
@endsection
