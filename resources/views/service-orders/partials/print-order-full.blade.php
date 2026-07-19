@php
    $createdAt = class_exists(\Morilog\Jalali\Jalalian::class)
        ? \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->created_at)->format('Y/m/d H:i')
        : $serviceOrder->created_at->format('Y/m/d H:i');
@endphp
<div class="prt-sheet prt-order-receipt prt-triple-receipt" dir="rtl" lang="fa">
    @include('service-orders.partials.print-section-intake', ['showHeader' => true, 'createdAt' => $createdAt])

    <div class="prt-tear-line" aria-hidden="true"></div>

    @include('service-orders.partials.print-section-delivery')

    <div class="prt-tear-line" aria-hidden="true"></div>

    <div class="prt-receipt-section prt-receipt-section-mini">
        <p class="prt-doc-title prt-doc-title-mini">مینی رسید — برچسب دستگاه</p>
        @include('service-orders.partials.print-mini-stub', ['createdAt' => $createdAt])
    </div>
</div>
