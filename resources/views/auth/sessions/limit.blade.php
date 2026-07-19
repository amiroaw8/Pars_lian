@extends('layouts.auth')

@section('title', 'محدودیت نشست‌های فعال')

@section('content')
<div class="text-center mb-6">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-4">
        <i class="ti ti-devices text-3xl"></i>
    </div>
    <h1 class="text-xl font-bold text-slate-800 mb-2">محدودیت نشست‌های فعال</h1>
    <p class="text-slate-500 text-sm">
        شما به حداکثر تعداد نشست‌های فعال ({{ $limit }} عدد) رسیده‌اید. لطفاً برای ادامه، یکی از نشست‌های زیر را ببندید.
    </p>
</div>

@if (session('success'))
<div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 text-sm flex items-center gap-2">
    <i class="ti ti-check-circle text-lg"></i>
    {{ session('success') }}
</div>
@endif

<div class="space-y-4">
    <ul class="divide-y divide-slate-100 bg-white border border-slate-200 rounded-lg overflow-hidden">
        @foreach ($sessions as $session)
        <li class="p-4 hover:bg-slate-50 transition-colors {{ $session->is_current ? 'bg-blue-50/50' : '' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="mt-1">
                        @if ($session->is_current)
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shadow-sm">
                                <i class="ti ti-device-desktop text-xl"></i>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center shadow-sm">
                                <i class="ti ti-device-mobile text-xl"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-medium text-slate-700 text-sm truncate max-w-[180px] block" title="{{ $session->user_agent }}">
                                {{ Str::limit($session->user_agent, 30) }}
                            </span>
                            @if ($session->is_current)
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200">نشست جاری</span>
                            @endif
                        </div>
                        <div class="space-y-1 text-xs text-slate-500">
                            <div class="flex items-center gap-1.5">
                                <i class="ti ti-world text-slate-400"></i>
                                <span dir="ltr">{{ $session->ip_address }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="ti ti-clock text-slate-400"></i>
                                آخرین فعالیت: {{ $session->last_activity }}
                            </div>
                        </div>
                    </div>
                </div>
                
                @if (!$session->is_current)
                <form action="{{ route('auth.sessions.destroy', $session->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="group flex items-center justify-center w-10 h-10 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all border border-transparent hover:border-red-100" title="خروج از این دستگاه">
                        <i class="ti ti-logout text-xl group-hover:scale-110 transition-transform"></i>
                    </button>
                </form>
                @else
                <div class="w-10 h-10 flex items-center justify-center">
                    <span class="text-xs text-slate-400 font-medium">شما</span>
                </div>
                @endif
            </div>
        </li>
        @endforeach
    </ul>
</div>

<div class="mt-8 text-center">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-slate-500 hover:text-slate-700 text-sm font-medium transition-colors">
            انصراف و خروج از حساب کاربری
        </button>
    </form>
</div>
@endsection
