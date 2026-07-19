@php
    use Morilog\Jalali\Jalalian;
@endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>پیش‌فاکتور — پارس لیان</title>
    @include('components.invoice-print-styles')
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()" style="padding:8px 16px;cursor:pointer;">چاپ / PDF</button>
        <button type="button" onclick="window.close()" style="padding:8px 16px;cursor:pointer;">بستن</button>
    </div>

    <div class="print-sheet">
        <div class="print-header">
            <div class="print-header-brand">
                <x-brand-logo size="print" mode="print" class="print-doc-logo" />
                <div class="print-doc-type">پیش‌فاکتور</div>
            </div>
            <div class="print-meta">
                <div>تاریخ: {{ Jalalian::now()->format('Y/m/d') }}</div>
            </div>
        </div>

        @if($customerName || $customerPhone || $customerAddress)
        <div style="margin-bottom:12px;font-size:11px;line-height:1.8;">
            @if($customerName)<div><strong>مشتری:</strong> {{ $customerName }}</div>@endif
            @if($customerPhone)<div><strong>تلفن:</strong> {{ $customerPhone }}</div>@endif
            @if($customerAddress)<div><strong>آدرس:</strong> {{ $customerAddress }}</div>@endif
        </div>
        @endif

        @if($description)
        <p style="margin-bottom:10px;font-size:11px;"><strong>توضیحات:</strong> {{ $description }}</p>
        @endif

        <table class="print-table">
            <thead>
                <tr>
                    <th style="width:8%">ردیف</th>
                    <th>شرح</th>
                    <th style="width:12%">تعداد</th>
                    <th style="width:18%">قیمت واحد</th>
                    <th style="width:18%">جمع</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['title'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ \App\Support\ShopFormat::moneyAmount($item['unit_price']) }}</td>
                    <td>{{ \App\Support\ShopFormat::moneyAmount($item['total']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="print-total">
            جمع کل: {{ \App\Support\ShopFormat::money($total) }}
            <div style="font-size:11px;font-weight:normal;margin-top:6px;color:#333;">
                {{ \App\Support\ShopFormat::amountInWords($total) }}
            </div>
        </div>
    </div>

    @if($autoPrint)
    <script>window.addEventListener('load', function() { window.print(); });</script>
    @endif
</body>
</html>
