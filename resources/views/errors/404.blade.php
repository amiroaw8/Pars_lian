@section('title', '۴۰۴ - یافت نشد')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="mb-8">
        <i class="ti ti-error-404 text-9xl text-blue-500 animate-bounce"></i>
    </div>
    <h1 class="text-4xl font-black text-gray-800 mb-4">{{ $message ?? 'صفحه مورد نظر یافت نشد.' }}</h1>
    <p class="text-gray-500 text-lg mb-10 max-w-md mx-auto">متاسفیم، اما صفحه‌ای که به دنبال آن هستید وجود ندارد یا جابجا شده است.</p>
    
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ url()->previous() }}" class="flex items-center gap-2 px-8 py-3 bg-gray-100 text-gray-700 rounded-2xl hover:bg-gray-200 transition-all font-bold">
            <i class="ti ti-arrow-right"></i>
            بازگشت به عقب
        </a>
        <a href="{{ url('/') }}" class="flex items-center gap-2 px-8 py-3 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all font-bold shadow-lg shadow-blue-200">
            <i class="ti ti-home"></i>
            صفحه اصلی
        </a>
    </div>
</div>
@endsection
