@php
    use Morilog\Jalali\Jalalian;
    $isPdf = $isPdf ?? request()->query('format') === 'pdf';
    $title = match($type) {
        'receipt' => 'رسید پذیرش دستگاه',
        'delivery' => 'رسید تحویل دستگاه',
        'invoice' => 'فاکتور خدمات و تعمیرات',
        'proforma' => 'پیش فاکتور خدمات',
        'label' => 'برچسب دستگاه',
        default => 'رسید'
    };
@endphp

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $serviceOrder->id }}</title>
    <link href="{{ asset('fonts/vazirmatn/misc/Farsi-Digits/Vazirmatn-FD-font-face.css') }}" rel="stylesheet" type="text/css" />
    @unless($isPdf)
        <script src="{{ asset('js/qrcode.min.js') }}"></script>
    @endunless
    <style>
        body {
            font-family: 'Vazirmatn FD', 'Vazirmatn', Tahoma, sans-serif;
            background: white;
            color: black;
            font-size: 13px;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            font-feature-settings: "kern" 1;
        }
        body, body * {
            font-family: 'Vazirmatn FD', 'Vazirmatn', Tahoma, sans-serif !important;
        }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .font-black { font-weight: 900; }
        .w-full { width: 100%; }
        .grid { display: grid; }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .gap-12 { gap: 3rem; }
        .mt-8 { margin-top: 2rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .p-8 { padding: 2rem; }
        .text-xs { font-size: 11px; }
        .text-sm { font-size: 12px; }
        .text-lg { font-size: 16px; }
        .text-xl { font-size: 18px; }
        .text-2xl { font-size: 22px; }
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
                font-size: 11px;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
            .print-page {
                max-height: none;
                overflow: visible;
            }
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        .table-bordered {
            width: 100%;
            border-collapse: collapse;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
        }
        .table-bordered th {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
        }
        .dotted-line {
            border-bottom: 1px dotted #000;
        }
        .signature-box {
            height: 80px;
            border: 1px solid #000;
            border-radius: 4px;
            margin-top: 10px;
        }
        .print-doc-logo {
            display: block;
            height: 52px;
            width: auto;
            max-width: 180px;
            max-height: 52px;
            object-fit: contain;
            border-radius: 8px;
        }
        @if($isPdf)
        .grid { display: block; width: 100%; }
        .grid-cols-2 > div { width: 48%; display: inline-block; vertical-align: top; margin-bottom: 8px; }
        .flex { display: block; }
        .justify-between { width: 100%; }
        @endif
    </style>

    @if($type === 'label')
    <style>
        @media print {
            body {
                font-size: 12px;
            }
        }
        @page {
            size: 8cm 5cm;
            margin: 0.2cm;
        }
    </style>
    @elseif($type === 'thermal')
    <style>
        @media print {
            body {
                width: 72mm;
                margin: 0 auto;
                font-size: 12px;
                padding: 5px;
            }
        }
        @page {
            size: 80mm auto;
            margin: 0;
        }
    </style>
    @endif
</head>
<body class="p-8">
    <div class="print-page">

    @unless($isPdf)
        <!-- Print Controls -->
        <div class="no-print fixed bottom-6 left-6 flex flex-col gap-3 z-50">
            <button onclick="window.print()" class="group flex items-center justify-center w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 hover:scale-110 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" title="چاپ">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span class="absolute right-full mr-3 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">چاپ کنید</span>
            </button>
            
            <button onclick="window.close()" class="group flex items-center justify-center w-12 h-12 bg-gray-500 text-white rounded-full shadow-lg hover:bg-gray-600 hover:scale-110 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" title="بستن">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="absolute right-full mr-3 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">بستن پنجره</span>
            </button>
        </div>
    @endunless

    @if($type === 'label')
        <!-- Mini Label Design -->
        <div class="border-2 border-black p-2 w-full h-full flex flex-col justify-between items-center text-center">
            <div class="text-lg font-black">{{ \App\Models\Setting::get('print_header_title', config('app.name', 'پارس لیان')) }}</div>
            <div class="text-4xl font-black my-1">{{ $serviceOrder->id }}</div>
            <div class="text-sm font-bold truncate w-full">{{ $serviceOrder->customer->name }}</div>
            <div class="text-xs mt-1">{{ Jalalian::now()->format('Y/m/d') }}</div>
            <div class="text-[10px] mt-1">{{ $serviceOrder->device->model ?? '' }}</div>
        </div>
    @elseif($type === 'thermal')
        <!-- Thermal Receipt Design -->
        <div class="text-center mb-4">
            <h2 class="text-xl font-black">{{ \App\Models\Setting::get('print_header_title', config('app.name', 'پارس لیان')) }}</h2>
            <div class="text-xs">{{ \App\Models\Setting::get('print_header_subtitle', 'مرکز تخصصی تعمیرات') }}</div>
            <div class="text-xs mt-1">{{ Jalalian::now()->format('Y/m/d H:i') }}</div>
            <div class="border-b-2 border-dashed border-black my-2"></div>
            <h3 class="text-lg font-bold">شماره سفارش: {{ $serviceOrder->id }}</h3>
        </div>

        <div class="text-xs mb-2">
            <div class="flex justify-between mb-1">
                <span>مشتری:</span>
                <span class="font-bold">{{ $serviceOrder->customer->name }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span>تلفن:</span>
                <span>{{ $serviceOrder->customer->phone }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span>دستگاه:</span>
                <span class="font-bold">{{ $serviceOrder->device->model ?? '---' }}</span>
            </div>
        </div>

        <div class="border-b border-dashed border-black my-2"></div>

        <div class="text-xs font-bold mb-1">شرح خدمات / قطعات:</div>
        <table class="w-full text-xs mb-4 table-bordered">
            <thead>
                <tr class="border-b border-black">
                    <th class="text-right py-1">شرح</th>
                    <th class="text-center w-8 py-1">تعداد</th>
                    <th class="text-center w-14 py-1">فی</th>
                    <th class="text-left w-16 py-1">قیمت کل</th>
                </tr>
            </thead>
            <tbody>
                 @php $total = 0; @endphp
                 @foreach($serviceOrder->repairItems as $item)
                    @php 
                        $itemTotal = $item->cost * $item->quantity;
                        $total += $itemTotal;
                    @endphp
                    <tr>
                        <td class="py-1">{{ \Illuminate\Support\Str::limit($item->description ?? ($item->inventory ? $item->inventory->name : '---'), 15) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ number_format($item->cost) }}</td>
                        <td class="text-left">{{ number_format($itemTotal) }}</td>
                    </tr>
                 @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-black font-bold">
                    <td colspan="3" class="py-2 text-right">جمع کل:</td>
                    <td class="py-2 text-left">{{ number_format($total) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="text-center text-[10px] mt-4">
            <p>{{ \App\Models\Setting::get('print_thermal_footer_1', 'با تشکر از انتخاب شما') }}</p>
            <p>{{ \App\Models\Setting::get('print_thermal_footer_2', 'www.parslian.com') }}</p>
        </div>

        <div class="flex justify-center my-2">
             <div id="qrcode-thermal"></div>
        </div>

        <div class="mt-4 text-center">
             <div class="inline-block border border-black p-1 text-[10px]">
                {{ $serviceOrder->id }}
             </div>
        </div>

        @unless($isPdf)
        <script>
            // Generate QR Code for Thermal
            if(document.getElementById("qrcode-thermal")) {
                new QRCode(document.getElementById("qrcode-thermal"), {
                    text: "{{ route('tracking.index', ['tracking_id' => $serviceOrder->id, 'phone' => $serviceOrder->receiver_phone]) }}",
                    width: 80,
                    height: 80
                });
            }
        </script>
        @endunless

    @else
        <!-- Standard A4 Header -->
        <div class="flex justify-between items-center mb-6 border-b-2 border-black pb-4">
            <div class="w-1/3">
                @if(\App\Support\BrandLogo::exists())
                    <img src="{{ \App\Support\BrandLogo::dataUri() }}" alt="پارس لیان" class="print-doc-logo">
                @endif
                <div class="text-sm text-gray-600 mt-2">{{ \App\Models\Setting::get('print_header_subtitle', 'مرکز تخصصی تعمیرات') }}</div>
            </div>
            <div class="w-1/3 text-center">
                <h2 class="text-lg font-bold border-2 border-black inline-block px-4 py-1 rounded">{{ $title }}</h2>
            </div>
            <div class="w-1/3 text-left text-sm space-y-1 relative pl-24">
                @if($isPdf)
                    <div class="absolute top-0 left-0 text-[9px] border border-black p-1">کد پیگیری: {{ $serviceOrder->id }}</div>
                @else
                    <div id="qrcode-a4" class="absolute top-0 left-0"></div>
                @endif
                <p><span class="font-bold">شماره سفارش:</span> <span class="text-lg font-mono">{{ $serviceOrder->id }}</span></p>
                <p><span class="font-bold">تاریخ:</span> {{ Jalalian::fromDateTime($serviceOrder->created_at)->format('Y/m/d') }}</p>
            </div>
        </div>

        @unless($isPdf)
        <script>
            // Generate QR Code for A4
            if(document.getElementById("qrcode-a4")) {
                new QRCode(document.getElementById("qrcode-a4"), {
                    text: "{{ route('tracking.index', ['tracking_id' => $serviceOrder->id, 'phone' => $serviceOrder->receiver_phone]) }}",
                    width: 80,
                    height: 80
                });
            }
        </script>
        @endunless

        <!-- Customer & Device Info -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="border border-black rounded p-3">
                <h3 class="font-bold border-b border-gray-300 mb-2 pb-1 bg-gray-100 -mx-3 -mt-3 px-3 pt-2 rounded-t text-sm">مشخصات مشتری</h3>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span><span class="font-semibold">نام:</span> {{ $serviceOrder->customer->name }}</span>
                        <span><span class="font-semibold">تلفن:</span> {{ $serviceOrder->customer->phone }}</span>
                    </div>
                    @if($serviceOrder->customer->address)
                        <div class="truncate"><span class="font-semibold">آدرس:</span> {{ $serviceOrder->customer->address }}</div>
                    @endif
                </div>
            </div>
            <div class="border border-black rounded p-3">
                <h3 class="font-bold border-b border-gray-300 mb-2 pb-1 bg-gray-100 -mx-3 -mt-3 px-3 pt-2 rounded-t text-sm">مشخصات دستگاه</h3>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span><span class="font-semibold">نوع:</span> {{ $serviceOrder->device->type ?? '---' }}</span>
                        <span><span class="font-semibold">مدل:</span> {{ $serviceOrder->device->model ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span><span class="font-semibold">شماره اموال:</span> {{ $serviceOrder->device->asset_number ?? '---' }}</span>
                        <span><span class="font-semibold">گارانتی:</span> {{ $serviceOrder->device->has_guarantee ? 'دارد' : 'ندارد' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content based on Type -->
        @if($type === 'receipt' || $type === 'delivery')
            <div class="mb-6 border border-black rounded">
                <div class="bg-gray-100 border-b border-black p-2 font-bold text-sm">اطلاعات تکمیلی پذیرش</div>
                <div class="p-3 text-sm grid grid-cols-1 gap-4">
                    <div>
                        <span class="font-bold block mb-1">ایراد ظاهری و فنی (اظهار مشتری):</span>
                        <p class="border-b dotted-line pb-1 min-h-[20px]">{{ $serviceOrder->fault }}</p>
                    </div>
                    <div>
                        <span class="font-bold block mb-1">لوازم همراه:</span>
                        <p class="border-b dotted-line pb-1 min-h-[20px]">{{ $serviceOrder->accessories ?? '---' }}</p>
                    </div>
                    @if($type === 'delivery')
                    <div>
                        <span class="font-bold block mb-1">اقدامات انجام شده:</span>
                        <p class="border-b dotted-line pb-1 min-h-[20px]">{{ $serviceOrder->repair_steps ?? '---' }}</p>
                    </div>
                    <div>
                        <span class="font-bold block mb-1">وضعیت نهایی:</span>
                        <p class="border-b dotted-line pb-1 min-h-[20px]">{{ $serviceOrder->status_label ?? '---' }}</p>
                    </div>
                    @if($serviceOrder->debt_amount && $serviceOrder->debt_amount > 0)
                    <div>
                        <span class="font-bold block mb-1">میزان بدهی:</span>
                        <p class="border-b dotted-line pb-1 min-h-[20px]">{{ number_format($serviceOrder->debt_amount) }} تومان</p>
                        @if($serviceOrder->debt_reason)
                        <p class="text-xs text-gray-600 mt-1">{{ $serviceOrder->debt_reason }}</p>
                        @endif
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        @endif

        @if($type === 'invoice' || $type === 'proforma' || $type === 'delivery')
            <!-- Invoice Items -->
            <div class="mb-6">
                <table class="table-bordered text-sm">
                    <thead>
                        <tr>
                            <th class="w-12">ردیف</th>
                            <th>شرح کالا / خدمات</th>
                            <th class="w-16">تعداد</th>
                            <th class="w-32">قیمت واحد (تومان)</th>
                            <th class="w-32">قیمت کل (تومان)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; $row = 1; @endphp
                        @foreach($serviceOrder->repairItems as $item)
                            @php 
                                $itemTotal = $item->cost * $item->quantity;
                                $total += $itemTotal;
                            @endphp
                            <tr>
                                <td>{{ $row++ }}</td>
                                <td class="text-right pr-2">
                                    {{ $item->description ?? ($item->inventory ? $item->inventory->name : '---') }}
                                    @if($item->item_type == 'part') <span class="text-xs text-gray-500">(قطعه)</span> @endif
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->cost) }}</td>
                                <td>{{ number_format($itemTotal) }}</td>
                            </tr>
                        @endforeach
                        <!-- Add Service Cost if exists -->
                        @if($serviceOrder->service_cost > 0)
                            @php $total += $serviceOrder->service_cost; @endphp
                            <tr>
                                <td>{{ $row++ }}</td>
                                <td class="text-right pr-2">اجرت خدمات و تعمیرات</td>
                                <td>1</td>
                                <td>{{ number_format($serviceOrder->service_cost) }}</td>
                                <td>{{ number_format($serviceOrder->service_cost) }}</td>
                            </tr>
                        @endif
                        
                        <!-- Empty rows for spacing if few items -->
                        @for($i = 0; $i < max(0, 5 - ($row - 1)); $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr class="font-bold bg-gray-100">
                            <td colspan="4" class="text-left pl-4">جمع کل</td>
                            <td>{{ number_format($total) }}</td>
                        </tr>
                        @php
                            $tax = 0; // Calculate tax if needed
                            $finalTotal = $total + $tax;
                        @endphp
                        <!-- 
                        <tr>
                            <td colspan="4" class="text-left pl-4">مالیات بر ارزش افزوده</td>
                            <td>{{ number_format($tax) }}</td>
                        </tr>
                        -->
                        <tr class="font-black text-lg bg-gray-200">
                            <td colspan="4" class="text-left pl-4">مبلغ قابل پرداخت</td>
                            <td>{{ number_format($finalTotal) }} تومان</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        <!-- Terms & Signatures -->
        <div class="mt-8">
            @if($type === 'receipt')
                <div class="text-xs text-justify mb-6 leading-relaxed border p-2 rounded bg-gray-50">
                    <p class="font-bold mb-1">شرایط پذیرش:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach(preg_split('/\r\n|\r|\n/', \App\Models\Setting::get('print_receipt_terms', "دستگاه فوق با مشخصات و ایراد ذکر شده تحویل گرفته شد.\nمرکز مسئولیتی در قبال اطلاعات شخصی روی دستگاه ندارد. لطفا قبل از تحویل بکاپ تهیه نمایید.\nهزینه نهایی پس از بررسی دقیق اعلام می‌گردد.\nمدت زمان تست دستگاه پس از تحویل ۲۴ ساعت می‌باشد.\nدر صورت عدم مراجعه تا یک ماه پس از اعلام تعمیر، مرکز مسئولیتی در قبال دستگاه ندارد.")) as $term)
                            @if(trim($term))
                                <li>{{ $term }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($type === 'invoice' || $type === 'proforma')
                <div class="text-xs text-justify mb-6 leading-relaxed border p-2 rounded bg-gray-50">
                    <p class="font-bold mb-1">توضیحات فاکتور:</p>
                    <p class="whitespace-pre-line">{{ \App\Models\Setting::get('print_invoice_notes', 'با تشکر از انتخاب شما') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-12 mt-8">
                <div class="text-center">
                    <p class="font-bold mb-2">مهر و امضای پذیرش / تعمیرکار</p>
                    <div class="signature-box"></div>
                </div>
                <div class="text-center">
                    <p class="font-bold mb-2">امضای مشتری</p>
                    <div class="signature-box"></div>
                    <p class="text-[10px] mt-1 text-gray-500">تایید صحت اطلاعات و خدمات</p>
                </div>
            </div>
        </div>
    @endif

    </div>{{-- /.print-page --}}

    @if(!empty($autoPrint))
    <script>
        window.addEventListener('load', function() {
            window.focus();
            window.print();
        });
    </script>
    @endif

</body>
</html>
