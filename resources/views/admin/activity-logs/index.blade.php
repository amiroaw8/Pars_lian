@extends('layouts.admin')

@section('title', 'لاگ فعالیت‌های سیستم - پارس لیان')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800">لاگ فعالیت‌های سیستم</h1>
            <p class="text-slate-500 text-sm mt-1">مشاهده و ممیزی تمام تغییرات و فعالیت‌های حساس</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">رویداد</label>
                <select name="event" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="">همه رویدادها</option>
                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>ایجاد</option>
                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>ویرایش</option>
                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>حذف</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">نوع موجودیت</label>
                <input type="text" name="loggable_type" value="{{ request('loggable_type') }}" placeholder="مثلاً Product..." class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            <div class="flex items-end gap-2 col-span-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-xl font-bold text-sm transition-all">جستجو و فیلتر</button>
                <a href="{{ route('admin.activity-logs.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 p-3 rounded-xl transition-all">
                    <i class="ti ti-refresh text-xl"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">کاربر</th>
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">رویداد</th>
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">موجودیت</th>
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">IP و مرورگر</th>
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">زمان</th>
                        <th class="px-6 py-5 text-xs font-black text-slate-500 uppercase tracking-widest">جزئیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i class="ti ti-user text-xl"></i>
                                </div>
                                <div>
                                    <span class="block font-bold text-slate-800">{{ $log->user->name ?? 'سیستم' }}</span>
                                    <span class="block text-xs text-slate-400">{{ $log->user->email ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $colors = [
                                    'created' => 'bg-emerald-50 text-emerald-600',
                                    'updated' => 'bg-blue-50 text-blue-600',
                                    'deleted' => 'bg-rose-50 text-rose-600',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-lg text-xs font-black {{ $colors[$log->event] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $log->eventLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm font-medium text-slate-600 block">{{ $log->modelLabel() }}</span>
                            <span class="text-xs text-slate-400 block mt-1">ID: {{ $log->loggable_id }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-xs font-mono text-slate-400 block">{{ $log->ip_address }}</span>
                            <span class="text-[10px] text-slate-300 block truncate max-w-[150px]" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-sm text-slate-600 font-bold block">{{ \Morilog\Jalali\Jalalian::fromDateTime($log->created_at)->format('Y/m/d') }}</span>
                            <span class="text-xs text-slate-400 block mt-1">{{ \Morilog\Jalali\Jalalian::fromDateTime($log->created_at)->format('H:i') }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <button onclick="showLogDetails('{{ $log->id }}')" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-800 hover:text-white transition-all flex items-center justify-center">
                                <i class="ti ti-eye text-xl"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-bold">موردی یافت نشد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-5 bg-slate-50 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal for details (Basic Implementation) -->
<div id="logModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden p-4" aria-hidden="true">
    <div class="flex items-center justify-center min-h-full">
    <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-xl font-black text-slate-800">جزئیات تغییرات</h2>
            <button onclick="closeLogModal()" class="text-slate-400 hover:text-rose-600 transition-colors">
                <i class="ti ti-x text-2xl"></i>
            </button>
        </div>
        <div id="logModalContent" class="p-6 max-h-[60vh] overflow-y-auto text-base leading-relaxed text-slate-800">
            <!-- Content here -->
        </div>
    </div>
    </div>
</div>

<script>
    function showLogDetails(id) {
        const modal = document.getElementById('logModal');
        const content = document.getElementById('logModalContent');
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        content.innerHTML = '<div class="flex justify-center p-8"><i class="ti ti-loader-2 animate-spin text-4xl text-blue-500"></i></div>';
        
        // Simulating data (You could extend the controller to return JSON)
        fetch(@json(route('admin.activity-logs.show', ['activityLog' => 0])).replace('/0', '/' + id))
            .then(response => {
                if (!response.ok) throw new Error('load failed');
                return response.text();
            })
            .then(html => {
               content.innerHTML = html;
            })
            .catch(() => {
               content.innerHTML = '<p class="text-rose-600 font-bold text-center py-8">خطا در بارگذاری جزئیات.</p>';
            });
    }

    function closeLogModal() {
        const modal = document.getElementById('logModal');
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
</script>
@endsection
