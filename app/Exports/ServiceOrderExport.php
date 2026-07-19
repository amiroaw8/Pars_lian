<?php

namespace App\Exports;

use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceOrderExport
{
    /**
     * Export service orders to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = ServiceOrder::with(['customer', 'device', 'technician']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                    ->orWhereHas('device', fn($dq) => $dq->where('type', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%")->orWhere('asset_number', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        $serviceOrders = $query->latest()->get();
        $filename = 'service-orders-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $callback = function () use ($serviceOrders) {
            $file = fopen('php://output', 'w');

            // BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, [
                'کد پیگیری',
                'نام مشتری',
                'تلفن مشتری',
                'نوع دستگاه',
                'مدل دستگاه',
                'شماره اموال',
                'وضعیت',
                'نوع سرویس',
                'تاریخ ثبت',
                'تعمیرکار',
                'توضیحات',
            ]);

            // Data
            foreach ($serviceOrders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->customer->name ?? '',
                    $order->customer->phone ?? '',
                    $order->device->type ?? '',
                    $order->device->model ?? '',
                    $order->device->asset_number ?? '',
                    $order->status->label(),
                    $order->service_type == 'in_company' ? 'در شرکت' : 'در محل',
                    $order->created_at->format('Y/m/d H:i'),
                    $order->technician->name ?? '',
                    substr($order->fault, 0, 100) . (strlen($order->fault) > 100 ? '...' : ''),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
