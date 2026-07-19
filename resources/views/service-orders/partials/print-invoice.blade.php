@php
    $docTitle = $docTitle ?? 'فاکتور خدمات و تعمیرات';
@endphp
<div class="prt-sheet" dir="rtl" lang="fa">
    @include('service-orders.partials.print-header', ['docTitle' => $docTitle ?? 'فاکتور خدمات و تعمیرات'])

    <div class="prt-grid-2">
        <div class="prt-box">
            <p class="prt-box-title">اطلاعات مشتری</p>
            <div class="prt-row"><span>نام</span><span>{{ $serviceOrder->customer->name }}</span></div>
            <div class="prt-row"><span>تلفن</span><span style="direction:ltr">{{ $serviceOrder->customer->phone }}</span></div>
            @if($serviceOrder->receiver_name)
            <div class="prt-row"><span>تحویل‌دهنده</span><span>{{ $serviceOrder->receiver_name }}</span></div>
            @endif
        </div>
        <div class="prt-box">
            <p class="prt-box-title">اطلاعات دستگاه</p>
            <div class="prt-row"><span>نوع</span><span>{{ $serviceOrder->device->type ?? '—' }}</span></div>
            <div class="prt-row"><span>مدل</span><span>{{ $serviceOrder->device->model ?? '—' }}</span></div>
            @if($serviceOrder->device->asset_number ?? null)
            <div class="prt-row"><span>شماره اموال</span><span>{{ $serviceOrder->device->asset_number }}</span></div>
            @endif
        </div>
    </div>

    <div class="prt-section">
        <p class="prt-section-title">اقدامات انجام‌شده و هزینه‌ها</p>
        @if($serviceOrder->repairItems->count() > 0)
        <table class="print-table">
            <thead>
                <tr>
                    <th style="text-align:right;width:45%">شرح اقدام / قطعه</th>
                    <th>تعداد</th>
                    <th>قیمت واحد</th>
                    <th>جمع (تومان)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceOrder->repairItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}. {{ $item->name }}</td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:center">{{ number_format($item->cost) }}</td>
                    <td style="text-align:center;font-weight:700">{{ number_format($item->cost * $item->quantity) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:left;font-size:10pt">جمع کل قابل پرداخت</td>
                    <td style="text-align:center;font-size:11pt">{{ number_format($serviceOrder->service_cost) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="prt-text-block">اقداماتی ثبت نشده است.</div>
        @endif
    </div>

    <div class="prt-total-box">
        <span>مبلغ قابل پرداخت</span>
        <span>{{ number_format($serviceOrder->service_cost) }} <small>تومان</small></span>
    </div>

    <div class="prt-stamp-only">
        <div class="prt-stamp-box">مهر یا امضای شرکت</div>
    </div>

    <div class="prt-footer">
        <span>پارس لیان — فاکتور رسمی خدمات</span>
        <span>سفارش <x-hash-ref :value="$serviceOrder->id" /></span>
    </div>
</div>
