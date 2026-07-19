@php
    $deliveredAt = $serviceOrder->repair_completed_at ?? now();
    $deliveredLabel = class_exists(\Morilog\Jalali\Jalalian::class)
        ? \Morilog\Jalali\Jalalian::fromCarbon($deliveredAt)->format('Y/m/d H:i')
        : $deliveredAt->format('Y/m/d H:i');
@endphp
<div class="prt-receipt-section">
    <p class="prt-doc-title">رسید تحویل دستگاه</p>

    <div class="prt-grid-2">
        <div class="prt-box">
            <p class="prt-box-title">مشتری</p>
            <div class="prt-row"><span>نام</span><span>{{ $serviceOrder->customer->name }}</span></div>
            <div class="prt-row"><span>تلفن</span><span class="prt-ltr">{{ $serviceOrder->customer->phone }}</span></div>
        </div>
        <div class="prt-box">
            <p class="prt-box-title">دستگاه</p>
            <div class="prt-row"><span>نوع</span><span>{{ $serviceOrder->device->type ?? '—' }}</span></div>
            <div class="prt-row"><span>مدل</span><span>{{ $serviceOrder->device->model ?? '—' }}</span></div>
            <div class="prt-row"><span>تاریخ تحویل</span><span>{{ $deliveredLabel }}</span></div>
        </div>
    </div>

    @if($serviceOrder->repairItems->count() > 0)
    <div class="prt-section">
        <p class="prt-section-title">خدمات / قطعات</p>
        <table class="print-table prt-table-compact">
            <thead>
                <tr>
                    <th style="text-align:right">شرح</th>
                    <th>تعداد</th>
                    <th>قیمت واحد</th>
                    <th>جمع</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceOrder->repairItems as $item)
                <tr>
                    <td style="text-align:right">{{ $item->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->cost) }}</td>
                    <td>{{ number_format($item->cost * $item->quantity) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:left">جمع کل</td>
                    <td>{{ number_format($serviceOrder->service_cost) }} تومان</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <div class="prt-signatures prt-signatures-compact">
        <div>
            <div class="prt-sign-box"></div>
            <p class="prt-sign-label">مهر و امضای شرکت</p>
        </div>
        <div>
            <div class="prt-sign-box"></div>
            <p class="prt-sign-label">امضای تحویل‌گیرنده</p>
        </div>
    </div>
</div>
