<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use App\Models\AccountingSale;
use App\Models\AccountingService;
use App\Models\Order;
use App\Services\AccountingManager;
use App\Services\CustomerFinancialInsights;
use App\Http\Requests\AccountingServiceRequest;
use App\Models\AccountingExpense;
use App\Models\ServiceOrder;
use App\Enums\ServiceOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;
use Illuminate\Database\Eloquent\Builder;

class AccountingController extends Controller
{
    public function __construct(
        private readonly AccountingManager $accountingManager
    ) {}

    public function index(Request $request)
    {
        if (! Auth::user()->canManageAccounting()) {
            abort(403, 'شما اجازه دسترسی به بخش حسابداری را ندارید.');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $shopOrdersQuery = Order::query()->with('user.customer')->onlineShop();
        $salesQuery = AccountingSale::with(['customer', 'order.user.customer']);
        $servicesQuery = AccountingService::with(['serviceOrder.customer', 'technician']);
        $expensesQuery = AccountingExpense::query();

        if ($startDate) {
            try {
                $convertedStart = Jalalian::fromFormat('Y/m/d', $startDate)->toCarbon();
                $shopOrdersQuery->whereDate('created_at', '>=', $convertedStart);
                $salesQuery->whereDate('transaction_date', '>=', $convertedStart);
                $servicesQuery->whereDate('transaction_date', '>=', $convertedStart);
                $expensesQuery->whereDate('expense_date', '>=', $convertedStart);
            } catch (\Exception $e) {
                // Fallback or ignore invalid format
            }
        }

        if ($endDate) {
            try {
                $convertedEnd = Jalalian::fromFormat('Y/m/d', $endDate)->toCarbon();
                $shopOrdersQuery->whereDate('created_at', '<=', $convertedEnd);
                $salesQuery->whereDate('transaction_date', '<=', $convertedEnd);
                $servicesQuery->whereDate('transaction_date', '<=', $convertedEnd);
                $expensesQuery->whereDate('expense_date', '<=', $convertedEnd);
            } catch (\Exception $e) {
                // Fallback or ignore invalid format
            }
        }

        $shopOrders = $shopOrdersQuery->latest()->paginate(10, ['*'], 'shop_page');
        $sales = $salesQuery->latest('transaction_date')->paginate(10, ['*'], 'sales_page');
        $services = $servicesQuery->latest('transaction_date')->paginate(10, ['*'], 'services_page');

        $repairOrdersQuery = ServiceOrder::query()
            ->with(['customer', 'device', 'technician'])
            ->whereIn('status', [
                ServiceOrderStatus::ACCOUNTING,
                ServiceOrderStatus::READY,
                ServiceOrderStatus::DELIVERED,
                ServiceOrderStatus::REPAIRING,
                ServiceOrderStatus::PENDING_PARTS,
                ServiceOrderStatus::SENT_TO_WORKSHOP,
            ]);
        if ($startDate) {
            try {
                $repairOrdersQuery->whereDate('created_at', '>=', Jalalian::fromFormat('Y/m/d', $startDate)->toCarbon());
            } catch (\Exception $e) {
            }
        }
        if ($endDate) {
            try {
                $repairOrdersQuery->whereDate('created_at', '<=', Jalalian::fromFormat('Y/m/d', $endDate)->toCarbon());
            } catch (\Exception $e) {
            }
        }
        $recentRepairOrders = $repairOrdersQuery->latest()->paginate(10, ['*'], 'repair_page');
        $posSalesQuery = \App\Models\Order::query()->posSales()->with('user');
        if ($startDate) {
            try {
                $convertedStart = Jalalian::fromFormat('Y/m/d', $startDate)->toCarbon();
                $posSalesQuery->whereDate('created_at', '>=', $convertedStart);
            } catch (\Exception $e) {
            }
        }
        if ($endDate) {
            try {
                $convertedEnd = Jalalian::fromFormat('Y/m/d', $endDate)->toCarbon();
                $posSalesQuery->whereDate('created_at', '<=', $convertedEnd);
            } catch (\Exception $e) {
            }
        }
        $posSales = $posSalesQuery->latest()->paginate(10, ['*'], 'pos_page');

        if ($startDate || $endDate) {
            $totals = $this->buildAccountingTotals(
                fn (Builder $q) => $this->applyDateFilter($q, $startDate, $endDate)
            );
        } else {
            $totals = Cache::remember('accounting_totals_v6', config('settings.accounting_cache_ttl', 3600), function () {
                return $this->buildAccountingTotals(fn (Builder $q) => $q);
            });
        }

        $insights = app(CustomerFinancialInsights::class);

        return view('accounting.index', array_merge([
            'shopOrders' => $shopOrders,
            'sales' => $sales,
            'services' => $services,
            'posSales' => $posSales,
            'recentRepairOrders' => $recentRepairOrders,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'debtorCustomers' => $insights->debtors(10),
            'goodPayerCustomers' => $insights->goodPayers(10),
            'valuableCustomers' => $insights->valuable(10),
        ], $totals));
    }

    public function export(Request $request)
    {
        if (! Auth::user()->canManageAccounting()) {
            abort(403, 'شما اجازه دریافت خروجی حسابداری را ندارید.');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type', 'all'); // all, sales, services

        $filename = "accounting_report_" . now()->format('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($startDate, $endDate, $type) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, ['گزارش مالی پارس لیان']);
            fputcsv($file, ['از تاریخ:', $startDate ?: 'ابتدا', 'تا تاریخ:', $endDate ?: 'امروز']);
            fputcsv($file, []); // Empty line

            if ($type === 'all' || $type === 'sales') {
                fputcsv($file, ['--- فروشگاه و فروش دستی ---']);
                fputcsv($file, ['تاریخ', 'شرح', 'مبلغ', 'مشتری', 'نوع']);
                
                $sales = AccountingSale::with('customer')
                    ->when($startDate, fn($q) => $q->whereDate('transaction_date', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate('transaction_date', '<=', $endDate))
                    ->get();
                
                foreach ($sales as $sale) {
                    fputcsv($file, [$sale->transaction_date, $sale->description, $sale->amount, $sale->customer?->name, 'دستی']);
                }

                $shopOrders = \App\Models\Order::with('user')
                    ->where('payment_status', \App\Enums\PaymentStatus::PAID)
                    ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                    ->get();

                foreach ($shopOrders as $order) {
                    fputcsv($file, [$order->created_at->format('Y-m-d'), "سفارش #" . $order->order_number, $order->total, $order->user->name, 'آنلاین']);
                }
                fputcsv($file, []);
            }

            if ($type === 'all' || $type === 'services') {
                fputcsv($file, ['--- خدمات و تعمیرات ---']);
                fputcsv($file, ['تاریخ', 'شرح', 'مبلغ', 'تکنسین', 'شماره سفارش']);

                $services = AccountingService::with(['serviceOrder.customer', 'technician'])
                    ->when($startDate, fn($q) => $q->whereDate('transaction_date', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate('transaction_date', '<=', $endDate))
                    ->get();

                foreach ($services as $service) {
                    fputcsv($file, [
                        $service->transaction_date, 
                        $service->description, 
                        $service->amount, 
                        $service->technician?->name, 
                        $service->service_order_id
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeService(AccountingServiceRequest $request)
    {
        if (! Auth::user()->canManageAccounting()) {
            abort(403, 'شما اجازه ثبت درآمد خدمات را ندارید.');
        }

        $this->accountingManager->recordService(
            $request->amount,
            $request->description,
            $request->service_order_id,
            $request->technician_id,
            $request->transaction_date
        );

        return redirect()->route('automation.accounting.index')
            ->with('success', 'دریافت سرویس با موفقیت ثبت شد.');
    }

    /**
     * @param  callable(Builder): Builder  $applyFilters
     * @return array<string, int|float>
     */
    private function buildAccountingTotals(callable $applyFilters): array
    {
        $manualQuery = $applyFilters(
            $this->paidAccountingSalesQuery(
                AccountingSale::query()->whereNull('order_id')
            )
        );
        $onlineQuery = $applyFilters(
            Order::query()->paid()->onlineShop()
        );
        $posQuery = $applyFilters(
            Order::query()->paid()->posSales()
        );

        $servicesQuery = $applyFilters(
            AccountingService::query()->where('payment_status', 'paid')
        );
        $expensesQuery = $applyFilters(AccountingExpense::query());

        $totalManualSales = (int) (clone $manualQuery)->sum('amount');
        $totalShopSales = (int) (clone $onlineQuery)->sum('total');
        $totalPosSales = (int) (clone $posQuery)->sum('total');
        $totalInPersonSales = $totalPosSales + $totalManualSales;
        $inPersonCount = (int) (clone $posQuery)->count() + (int) (clone $manualQuery)->count();
        $totalServices = (int) (clone $servicesQuery)->sum('amount');
        $totalSales = $totalShopSales + $totalInPersonSales;
        $totalIncome = $totalSales + $totalServices;
        $totalExpenses = (int) $expensesQuery->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        $unrealized = $this->buildUnrealizedProfit($applyFilters);

        return [
            'totalManualSales' => $totalManualSales,
            'totalShopSales' => $totalShopSales,
            'totalPosSales' => $totalPosSales,
            'totalInPersonSales' => $totalInPersonSales,
            'totalSales' => $totalSales,
            'totalServices' => $totalServices,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'unrealizedProfit' => $unrealized['total'],
            'unrealizedBreakdown' => $unrealized['breakdown'],
            'financialBreakdown' => [
                [
                    'key' => 'shop',
                    'label' => 'فروش آنلاین فروشگاه',
                    'amount' => $totalShopSales,
                    'count' => (int) (clone $onlineQuery)->count(),
                    'count_label' => 'سفارش پرداخت‌شده',
                    'source' => 'فقط سفارشات فروشگاه آنلاین با وضعیت پرداخت‌شده (ارسال تیپاکس، دکاپست یا اسنپ)',
                ],
                [
                    'key' => 'in_person',
                    'label' => 'فروش حضوری',
                    'amount' => $totalInPersonSales,
                    'count' => $inPersonCount,
                    'count_label' => 'فاکتور پرداخت‌شده',
                    'source' => 'فقط فاکتورهای پرداخت‌شده از صندوق فروش حضوری (POS) و تراکنش‌های فروش تکمیل‌شده مرتبط',
                ],
                [
                    'key' => 'services',
                    'label' => 'درآمد خدمات تعمیر',
                    'amount' => $totalServices,
                    'count' => (int) (clone $servicesQuery)->count(),
                    'count_label' => 'تراکنش پرداخت‌شده',
                    'source' => 'فقط تراکنش‌های خدمات تعمیر با وضعیت «پرداخت‌شده» در حسابداری',
                ],
                [
                    'key' => 'expenses',
                    'label' => 'هزینه‌های عملیاتی',
                    'amount' => $totalExpenses,
                    'count' => (int) (clone $expensesQuery)->count(),
                    'count_label' => 'ردیف هزینه',
                    'source' => 'جمع هزینه‌های ثبت‌شده در بخش مدیریت هزینه‌های حسابداری',
                ],
            ],
        ];
    }

  /**
     * @param  Builder<AccountingSale>  $query
     * @return Builder<AccountingSale>
     */
    private function paidAccountingSalesQuery(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', 'completed')->orWhereNull('status');
        });
    }

    /**
     * @param  callable(Builder): Builder  $applyFilters
     * @return array{total: int, breakdown: list<array{key: string, label: string, amount: int, count: int, count_label: string, source: string}>}
     */
    private function buildUnrealizedProfit(callable $applyFilters): array
    {
        $unpaidOnlineQuery = $applyFilters(
            Order::query()
                ->onlineShop()
                ->where('payment_status', PaymentStatus::PENDING)
                ->where('status', '!=', OrderStatus::CANCELLED)
        );
        $unpaidPosQuery = $applyFilters(
            Order::query()
                ->posSales()
                ->where('payment_status', PaymentStatus::PENDING)
                ->where('status', '!=', OrderStatus::CANCELLED)
        );
        $unpaidManualSalesQuery = $applyFilters(
            AccountingSale::query()
                ->whereNull('order_id')
                ->where('status', 'pending')
        );
        $unpaidServicesQuery = $applyFilters(
            AccountingService::query()->where('payment_status', 'unpaid')
        );
        $partialServicesQuery = $applyFilters(
            AccountingService::query()->where('payment_status', 'partial')
        );

        $unpaidShop = (int) (clone $unpaidOnlineQuery)->sum('total');
        $unpaidPos = (int) (clone $unpaidPosQuery)->sum('total');
        $unpaidManual = (int) (clone $unpaidManualSalesQuery)->sum('amount');
        $unpaidServices = (int) (clone $unpaidServicesQuery)->sum('amount');
        $partialServices = (int) (clone $partialServicesQuery)->sum('amount');
        $unpaidInPerson = $unpaidPos + $unpaidManual;

        $breakdown = [
            [
                'key' => 'shop_unpaid',
                'label' => 'فروش آنلاین فروشگاه (پرداخت نشده)',
                'amount' => $unpaidShop,
                'count' => (int) (clone $unpaidOnlineQuery)->count(),
                'count_label' => 'سفارش معوق',
                'source' => 'سفارشات آنلاین فروشگاه با وضعیت «در انتظار پرداخت» که هنوز تسویه نشده‌اند',
            ],
            [
                'key' => 'pos_unpaid',
                'label' => 'فروش حضوری — صندوق (پرداخت نشده)',
                'amount' => $unpaidPos,
                'count' => (int) (clone $unpaidPosQuery)->count(),
                'count_label' => 'فاکتور معوق',
                'source' => 'فاکتورهای فروش حضوری (POS) با وضعیت پرداخت «در انتظار» یا ثبت‌شده به‌صورت بدهی',
            ],
            [
                'key' => 'manual_unpaid',
                'label' => 'فروش حضوری — بدهی ثبت‌شده',
                'amount' => $unpaidManual,
                'count' => (int) (clone $unpaidManualSalesQuery)->count(),
                'count_label' => 'تراکنش معوق',
                'source' => 'تراکنش‌های فروش با وضعیت «در انتظار» که مستقیماً در حسابداری ثبت شده‌اند (بدون اتصال به سفارش سیستمی)',
            ],
            [
                'key' => 'services_unpaid',
                'label' => 'خدمات تعمیر (پرداخت نشده)',
                'amount' => $unpaidServices,
                'count' => (int) (clone $unpaidServicesQuery)->count(),
                'count_label' => 'تراکنش',
                'source' => 'تراکنش‌های خدمات تعمیر با وضعیت «پرداخت نشده» در حسابداری',
            ],
            [
                'key' => 'services_partial',
                'label' => 'خدمات تعمیر (پرداخت جزئی)',
                'amount' => $partialServices,
                'count' => (int) (clone $partialServicesQuery)->count(),
                'count_label' => 'تراکنش',
                'source' => 'تراکنش‌های خدمات تعمیر با وضعیت «پرداخت جزئی» که هنوز به‌طور کامل تسویه نشده‌اند',
            ],
        ];

        $total = $unpaidShop + $unpaidInPerson + $unpaidServices + $partialServices;

        return [
            'total' => $total,
            'breakdown' => $breakdown,
        ];
    }

    private function applyDateFilter(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        $model = $query->getModel();
        $dateColumn = match (true) {
            $model instanceof Order => 'created_at',
            $model instanceof ServiceOrder => 'created_at',
            $model instanceof AccountingExpense => 'expense_date',
            default => 'transaction_date',
        };

        if ($startDate) {
            try {
                $query->whereDate($dateColumn, '>=', Jalalian::fromFormat('Y/m/d', $startDate)->toCarbon());
            } catch (\Exception $e) {
            }
        }

        if ($endDate) {
            try {
                $query->whereDate($dateColumn, '<=', Jalalian::fromFormat('Y/m/d', $endDate)->toCarbon());
            } catch (\Exception $e) {
            }
        }

        return $query;
    }
}
