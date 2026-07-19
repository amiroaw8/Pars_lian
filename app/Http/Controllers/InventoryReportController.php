<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use App\Support\JalaliDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Morilog\Jalali\Jalalian;

class InventoryReportController extends Controller
{
    public function index()
    {
        return view('inventory.reports.index');
    }

    private function getBalanceData(Request $request)
    {
        $query = Inventory::query();

        if ($request->filled('category')) {
            $query->where('type', $request->category);
        }

        $items = $query->get();

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            $start = JalaliDate::startOfDay($startDate);
            $end = JalaliDate::endOfDay($endDate);

            if ($start && $end) {
                /** @var \App\Models\Inventory $item */
                foreach ($items as $item) {
                    $inPeriod = (int) $item->transactions()
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('quantity_change');

                    $in = (int) $item->transactions()
                        ->whereBetween('created_at', [$start, $end])
                        ->where('quantity_change', '>', 0)
                        ->sum('quantity_change');

                    $out = abs((int) $item->transactions()
                        ->whereBetween('created_at', [$start, $end])
                        ->where('quantity_change', '<', 0)
                        ->sum('quantity_change'));

                    $item->opening_stock = $item->quantity - $inPeriod;
                    $item->total_in = $in;
                    $item->total_out = $out;
                    $item->closing_stock = $item->opening_stock + $inPeriod;
                }
            } else {
                $startDate = null;
                $endDate = null;
                $items = $this->applyBalanceWithoutDateFilter($items);
            }
        } else {
            $items = $this->applyBalanceWithoutDateFilter($items);
        }
        
        return compact('items', 'startDate', 'endDate');
    }

    private function applyBalanceWithoutDateFilter($items)
    {
        /** @var \App\Models\Inventory $item */
        foreach ($items as $item) {
            $item->total_in = (int) $item->transactions()->where('quantity_change', '>', 0)->sum('quantity_change');
            $item->total_out = abs((int) $item->transactions()->where('quantity_change', '<', 0)->sum('quantity_change'));
            $item->closing_stock = (int) $item->quantity;
            $item->opening_stock = max(0, $item->closing_stock - $item->total_in + $item->total_out);
        }

        return $items;
    }

    public function balance(Request $request)
    {
        $data = $this->getBalanceData($request);
        return view('inventory.reports.balance', $data);
    }

    public function exportBalance(Request $request)
    {
        $data = $this->getBalanceData($request);
        $items = $data['items'];

        $fileName = 'inventory-balance-' . date('Y-m-d') . '.csv';
        
        $headers = array(
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('کالا', 'SKU', 'موجودی ابتدای دوره', 'وارده', 'صادره', 'موجودی پایان دوره');

        $callback = function() use($items, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8 in Excel
            fputcsv($file, $columns);

            foreach ($items as $item) {
                $row = [
                    $item->name,
                    $item->sku,
                    $item->opening_stock,
                    $item->total_in,
                    $item->total_out,
                    $item->closing_stock
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function cardex(Request $request)
    {
        $inventoryId = $request->input('inventory_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transactions = collect();
        $inventory = null;

        if ($inventoryId) {
            $inventory = Inventory::findOrFail($inventoryId);
            $query = $inventory->transactions()->with('user');

            if ($startDate) {
                $from = JalaliDate::startOfDay($startDate);
                $query->where('created_at', '>=', $from ?? $startDate);
            }
            if ($endDate) {
                $to = JalaliDate::endOfDay($endDate);
                $query->where('created_at', '<=', $to ?? $endDate);
            }

            $transactions = $query->latest()->get();
        }

        $inventories = Inventory::select('id', 'name', 'sku', 'quantity', 'min_quantity')->get();

        return view('inventory.reports.cardex', compact('inventory', 'transactions', 'inventories', 'startDate', 'endDate'));
    }

    public function exportCardex(Request $request)
    {
        $inventoryId = $request->input('inventory_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$inventoryId) {
            return Redirect::back()->with('error', 'لطفا کالا را انتخاب کنید');
        }

        $inventory = Inventory::findOrFail($inventoryId);
        $query = $inventory->transactions()->with('user');

        if ($startDate) {
            $from = JalaliDate::startOfDay($startDate);
            $query->where('created_at', '>=', $from ?? $startDate);
        }
        if ($endDate) {
            $to = JalaliDate::endOfDay($endDate);
            $query->where('created_at', '<=', $to ?? $endDate);
        }

        $transactions = $query->latest()->get();

        $fileName = 'cardex-' . $inventory->sku . '-' . date('Y-m-d') . '.csv';
        
        $headers = array(
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('تاریخ', 'نوع تراکنش', 'ورده', 'صادره', 'مانده', 'کاربر', 'توضیحات');

        $callback = function() use($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($transactions as $transaction) {
                $type = match($transaction->transaction_type) {
                    'purchase' => 'خرید',
                    'sale' => 'فروش',
                    'use' => 'مصرف',
                    'return' => 'برگشت',
                    'warranty_sent' => 'ارسال گارانتی',
                    'warranty_return' => 'برگشت گارانتی',
                    default => $transaction->transaction_type
                };

                $in = $transaction->quantity_change > 0 ? $transaction->quantity_change : 0;
                $out = $transaction->quantity_change < 0 ? abs($transaction->quantity_change) : 0;
                
                $row = [
                    jalali_date($transaction->created_at, 'Y/m/d H:i'),
                    $type,
                    $in,
                    $out,
                    $transaction->new_quantity,
                    $transaction->user->name ?? 'سیستم',
                    $transaction->notes . 
                    ($transaction->receiver ? " - تحویل: {$transaction->receiver}" : "") . 
                    ($transaction->organization ? " - ارگان: {$transaction->organization}" : "")
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function transactions(Request $request)
    {
        $query = InventoryTransaction::with(['inventory', 'user']);

        if ($request->filled('start_date')) {
            $from = JalaliDate::startOfDay($request->start_date);
            if ($from) {
                $query->where('created_at', '>=', $from);
            }
        }
        if ($request->filled('end_date')) {
            $to = JalaliDate::endOfDay($request->end_date);
            if ($to) {
                $query->where('created_at', '<=', $to);
            }
        }
        if ($request->filled('inventory_id')) {
            $query->where('inventory_id', $request->inventory_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        $transactions = $query->latest()->paginate(50)->withQueryString();
        $inventories = Inventory::select('id', 'name')->get();
        $users = \App\Models\User::role([
            'admin', 'super_admin', 'technician', 'receptionist', 'warehouse', 'accountant',
        ])->select('id', 'name')->orderBy('name')->get();

        return view('inventory.reports.transactions', compact('transactions', 'inventories', 'users'));
    }

    public function exportTransactions(Request $request)
    {
        $query = InventoryTransaction::with(['inventory', 'user']);

        if ($request->filled('start_date')) {
            $from = JalaliDate::startOfDay($request->start_date);
            if ($from) {
                $query->where('created_at', '>=', $from);
            }
        }
        if ($request->filled('end_date')) {
            $to = JalaliDate::endOfDay($request->end_date);
            if ($to) {
                $query->where('created_at', '<=', $to);
            }
        }
        if ($request->filled('inventory_id')) {
            $query->where('inventory_id', $request->inventory_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        $transactions = $query->latest()->get();

        $fileName = 'inventory-transactions-' . date('Y-m-d') . '.csv';
        
        $headers = array(
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('تاریخ', 'کالا', 'نوع تراکنش', 'تعداد', 'موجودی جدید', 'کاربر', 'توضیحات');

        $callback = function() use($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($transactions as $transaction) {
                $type = match($transaction->transaction_type) {
                    'purchase' => 'خرید',
                    'sale' => 'فروش',
                    'use' => 'مصرف',
                    'return' => 'برگشت',
                    'warranty_sent' => 'ارسال گارانتی',
                    'warranty_return' => 'برگشت گارانتی',
                    default => $transaction->transaction_type
                };

                $row = [
                    jalali_date($transaction->created_at, 'Y/m/d H:i'),
                    $transaction->inventory->name,
                    $type,
                    $transaction->quantity_change,
                    $transaction->new_quantity,
                    $transaction->user ? $transaction->user->name : 'سیستم',
                    $transaction->description
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
