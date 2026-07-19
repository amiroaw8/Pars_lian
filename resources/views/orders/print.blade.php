@php
    use Morilog\Jalali\Jalalian;
    $title = match($type) {
        'invoice' => 'فاکتور فروش',
        'proforma' => 'پیش‌فاکتور فروش',
        'receipt' => 'رسید فروش',
        'delivery' => 'رسید تحویل کالا',
        default => 'فاکتور',
    };
@endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} — {{ $order->order_number }}</title>
    @include('components.invoice-print-styles')
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()" style="padding:8px 16px;cursor:pointer;">چاپ</button>
        <button type="button" onclick="window.close()" style="padding:8px 16px;cursor:pointer;">بستن</button>
    </div>

    <div class="print-sheet">
        <div class="print-header">
            <div class="print-header-brand">
                <x-brand-logo size="print" mode="print" class="print-doc-logo" />
                <div class="print-doc-type">{{ $title }}</div>
            </div>
            <div class="print-meta">
                <div><strong>شماره:</strong> {{ $order->order_number }}</div>
                <div><strong>تاریخ:</strong> {{ Jalalian::fromDateTime($order->created_at)->format('Y/m/d H:i') }}</div>
            </div>
        </div>

        <div style="margin-bottom:10px;font-size:11px;">
            <strong>مشتری:</strong>
            {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}
            — {{ $order->shipping_phone }}
        </div>

        <table class="print-table">
            <thead>
                <tr>
                    <th style="width:8%">ردیف</th>
                    <th>شرح کالا</th>
                    <th style="width:12%">تعداد</th>
                    <th style="width:18%">قیمت واحد</th>
                    <th style="width:18%">جمع</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->product_name ?? $item->product?->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ \App\Support\ShopFormat::moneyAmount($item->price) }}</td>
                    <td>{{ \App\Support\ShopFormat::moneyAmount($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="print-total">
            جمع کل: {{ \App\Support\ShopFormat::money($order->total) }}
            <div style="font-size:11px;font-weight:normal;margin-top:6px;color:#333;">
                {{ \App\Support\ShopFormat::amountInWords($order->total) }}
            </div>
        </div>

        @if($type === 'delivery')
        <p style="margin-top:12px;font-size:10px;">تحویل‌گیرنده با امضای خود صحت دریافت کالا را تایید می‌نماید.</p>
        @endif

        <div class="signatures">
            <div class="sign-box" style="max-width: 320px; margin: 0 auto;">
                <span>مهر یا امضای شرکت</span>
            </div>
        </div>
    </div>
</body>
</html>
