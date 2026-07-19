@php
    $createdAt = $createdAt ?? (class_exists(\Morilog\Jalali\Jalalian::class)
        ? \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->created_at)->format('Y/m/d H:i')
        : $serviceOrder->created_at->format('Y/m/d H:i'));
    $showHeader = $showHeader ?? false;
@endphp
<div class="prt-receipt-section">
    @if($showHeader)
        @include('service-orders.partials.print-header', ['docTitle' => 'رسید دریافت دستگاه توسط شرکت'])
    @else
        <p class="prt-doc-title">رسید دریافت دستگاه توسط شرکت</p>
    @endif

    <div class="prt-grid-2">
        <div class="prt-box">
            <p class="prt-box-title">مشتری</p>
            <div class="prt-row"><span>نام</span><span>{{ $serviceOrder->customer->name }}</span></div>
            <div class="prt-row"><span>تلفن</span><span class="prt-ltr">{{ $serviceOrder->customer->phone }}</span></div>
            <div class="prt-row"><span>تاریخ ثبت</span><span>{{ $createdAt }}</span></div>
        </div>
        <div class="prt-box">
            <p class="prt-box-title">دستگاه</p>
            <div class="prt-row"><span>نوع</span><span>{{ $serviceOrder->device->type ?? '—' }}</span></div>
            <div class="prt-row"><span>مدل</span><span>{{ $serviceOrder->device->model ?? '—' }}</span></div>
        </div>
    </div>

    <div class="prt-grid-2 prt-grid-compact">
        <div class="prt-section">
            <p class="prt-section-title">ایراد اعلام‌شده</p>
            <div class="prt-text-block prt-text-compact">{{ $serviceOrder->fault ?: '—' }}</div>
        </div>
        <div class="prt-section">
            <p class="prt-section-title">لوازم همراه</p>
            <div class="prt-text-block prt-text-compact">{{ $serviceOrder->accessories ?: 'موردی ثبت نشده' }}</div>
        </div>
    </div>

    @if($showHeader)
        @include('service-orders.partials.print-terms-block')
    @endif

    <div class="prt-signatures prt-signatures-compact">
        <div>
            <div class="prt-sign-box"></div>
            <p class="prt-sign-label">مهر و امضای پذیرش</p>
        </div>
        <div>
            <div class="prt-sign-box"></div>
            <p class="prt-sign-label">امضای مشتری</p>
        </div>
    </div>
</div>
