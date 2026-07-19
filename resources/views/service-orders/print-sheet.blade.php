<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پارس لیان — سفارش {{ $serviceOrder->id }}</title>
    @include('components.invoice-print-styles')
    @include('service-orders.partials.print-document-styles')
</head>
<body>
    <div class="no-print">
        <button type="button" onclick="window.print()" style="padding:8px 16px;cursor:pointer;">چاپ / PDF</button>
        <button type="button" onclick="window.close()" style="padding:8px 16px;cursor:pointer;">بستن</button>
    </div>

    <div class="print-sheet">
        @switch($layout)
            @case('invoice')
                @include('service-orders.partials.print-invoice', ['docTitle' => 'فاکتور خدمات (تعمیر)'])
                @break
            @case('sale')
                @include('service-orders.partials.print-invoice', ['docTitle' => 'فاکتور فروش'])
                @break
            @case('proforma')
                @include('service-orders.partials.print-invoice', ['docTitle' => 'پیش‌فاکتور'])
                @break
            @case('receipt')
                @include('service-orders.partials.print-receipt-intake')
                @break
            @case('delivery')
                @include('service-orders.partials.print-receipt-delivery')
                @break
            @case('mini')
                @include('service-orders.partials.print-mini-only')
                @break
            @default
                @include('service-orders.partials.print-order-full')
        @endswitch
    </div>

    @if(!empty($autoPrint) || request()->boolean('auto_print'))
    <script>window.addEventListener('load', function () { window.print(); });</script>
    @endif
</body>
</html>
