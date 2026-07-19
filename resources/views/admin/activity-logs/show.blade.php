<div class="space-y-6">
    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">اطلاعات کلی</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-slate-500 block text-xs">کاربر:</span>
                <span class="font-bold text-slate-800">{{ $activityLog->user?->name ?? '—' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-xs">رویداد:</span>
                <span class="font-bold text-slate-800">{{ $activityLog->eventLabel() }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-xs">موجودیت:</span>
                <span class="font-bold text-slate-800">{{ $activityLog->modelLabel() }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-xs">شناسه:</span>
                <span class="font-bold text-slate-800">{{ $activityLog->loggable_id }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-xs">IP:</span>
                <span class="font-bold text-slate-800">{{ $activityLog->ip_address ?? '—' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-xs">تاریخ:</span>
                <span class="font-bold text-slate-800">{{ jalali_date($activityLog->created_at, 'Y/m/d H:i:s') }}</span>
            </div>
        </div>
    </div>

    @php $changes = $activityLog->changeLines(); @endphp
    @if(!empty($changes))
        <div>
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">جزئیات تغییرات</h3>
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-2 text-sm text-slate-700">
                @foreach($changes as $line)
                    <div>{{ $line }}</div>
                @endforeach
            </div>
        </div>
    @elseif($activityLog->old_values || $activityLog->new_values)
        @if($activityLog->old_values)
        <div>
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">مقادیر قبلی</h3>
            <div class="bg-rose-50 rounded-2xl p-4 border border-rose-100 text-rose-700">
                <pre class="whitespace-pre-wrap text-xs">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif

        @if($activityLog->new_values)
        <div>
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">مقادیر جدید</h3>
            <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100 text-emerald-700">
                <pre class="whitespace-pre-wrap text-xs">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
        @endif
    @else
        <div class="text-center py-8 text-slate-400 font-bold">
            داده‌ای برای نمایش وجود ندارد.
        </div>
    @endif
</div>
