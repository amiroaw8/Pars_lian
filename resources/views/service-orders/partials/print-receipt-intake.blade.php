@php
    $createdAt = class_exists(\Morilog\Jalali\Jalalian::class)
        ? \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->created_at)->format('Y/m/d H:i')
        : $serviceOrder->created_at->format('Y/m/d H:i');
@endphp
<div class="prt-sheet prt-order-receipt" dir="rtl" lang="fa">
    @include('service-orders.partials.print-section-intake', ['showHeader' => true, 'createdAt' => $createdAt])
</div>
