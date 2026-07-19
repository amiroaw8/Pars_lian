@php
    use App\Support\CompanyProfile;

    $createdAt = class_exists(\Morilog\Jalali\Jalalian::class)
        ? \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->created_at)->format('Y/m/d H:i')
        : $serviceOrder->created_at->format('Y/m/d H:i');
@endphp
<div class="prt-sheet prt-order-receipt prt-mini-only" dir="rtl" lang="fa">
    @include('service-orders.partials.print-header', ['docTitle' => 'مینی رسید — برچسب دستگاه'])
    <div class="prt-tear-line" aria-hidden="true"></div>
    @include('service-orders.partials.print-mini-stub', ['createdAt' => $createdAt])
</div>
