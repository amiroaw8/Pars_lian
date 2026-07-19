<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Support\ShopFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RepairPrintController extends Controller
{
    private const PROFORMA_CACHE_PREFIX = 'service_proforma:';

    private function canAccessFinancialDocuments(): bool
    {
        $user = auth()->user();

        return $user && (
            $user->canManageAccounting()
            || $user->isReceptionist()
            || $user->isAdmin()
            || $user->canManageRepairs()
        );
    }

    public function proformaCreate(ServiceOrder $serviceOrder)
    {
        $this->authorize('view', $serviceOrder);

        if (! $this->canAccessFinancialDocuments()) {
            abort(403, 'شما اجازه دسترسی به پیش‌فاکتور را ندارید.');
        }

        $serviceOrder->load([
            'customer',
            'device',
            'repairItems' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
        ]);

        return view('service-orders.proforma-create', compact('serviceOrder'));
    }

    public function proformaStore(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorize('view', $serviceOrder);

        if (! $this->canAccessFinancialDocuments()) {
            abort(403, 'شما اجازه دسترسی به پیش‌فاکتور را ندارید.');
        }

        $items = collect($request->input('items', []))
            ->filter(fn ($item) => filled($item['title'] ?? null))
            ->values()
            ->all();

        $request->merge(['items' => $items]);

        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'customer_address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $payload = $this->buildProformaPayload($validated, $request, $serviceOrder);
        $token = Str::random(40);

        Cache::put(self::PROFORMA_CACHE_PREFIX.$token, $payload, now()->addHours(2));

        return redirect()->route('automation.repairs.proforma.print.show', [
            'serviceOrder' => $serviceOrder,
            'token' => $token,
        ]);
    }

    public function proformaPrintShow(ServiceOrder $serviceOrder, string $token)
    {
        $this->authorize('view', $serviceOrder);

        if (! $this->canAccessFinancialDocuments()) {
            abort(403, 'شما اجازه دسترسی به پیش‌فاکتور را ندارید.');
        }

        $payload = Cache::get(self::PROFORMA_CACHE_PREFIX.$token);

        if (! $payload || (int) ($payload['serviceOrderId'] ?? 0) !== (int) $serviceOrder->id) {
            return redirect()
                ->route('automation.repairs.proforma.create', $serviceOrder)
                ->with('error', 'اطلاعات پیش‌فاکتور منقضی یا یافت نشد. لطفاً دوباره فرم را تکمیل کنید.');
        }

        return view('accounting.proforma.print', $payload);
    }

    private function buildProformaPayload(array $validated, Request $request, ServiceOrder $serviceOrder): array
    {
        $items = collect($validated['items'])->map(function ($item) {
            $qty = (int) $item['quantity'];
            $price = (float) $item['unit_price'];

            return [
                'title' => $item['title'],
                'quantity' => $qty,
                'unit_price' => ShopFormat::toIntegerAmount($price),
                'total' => $qty * ShopFormat::toIntegerAmount($price),
            ];
        });

        $subtotal = $items->sum('total');

        return [
            'serviceOrderId' => $serviceOrder->id,
            'customerName' => $validated['customer_name'] ?? '',
            'customerPhone' => $validated['customer_phone'] ?? '',
            'customerAddress' => $validated['customer_address'] ?? '',
            'description' => $validated['description'] ?? '',
            'items' => $items->values()->all(),
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'autoPrint' => $request->boolean('auto_print', true),
        ];
    }

    public function show(Request $request, ServiceOrder $serviceOrder)
    {
        $type = $request->query('type', 'receipt');
        
        // Security Check: Only managers/admins can view financial documents
        if (in_array($type, ['invoice', 'proforma'])) {
             if (! (auth()->user()->canManageAccounting() || auth()->user()->isReceptionist() || auth()->user()->isAdmin() || auth()->user()->canManageRepairs())) {
                 abort(403, 'شما اجازه دسترسی به اسناد مالی را ندارید.');
             }
        }

        $serviceOrder->load(['customer', 'device', 'technician', 'repairItems']);

        // PDF downloads use browser print (same as printOrder) — dompdf lacks proper Persian font support
        if ($request->query('format') === 'pdf') {
            return redirect()->route('automation.repairs.print', [
                'serviceOrder' => $serviceOrder,
                'type' => $type,
                'auto_print' => 1,
            ]);
        }

        return view('repairs.print', [
            'serviceOrder' => $serviceOrder,
            'type' => $type,
            'isPdf' => false,
            'autoPrint' => $request->boolean('auto_print'),
        ]);
    }

    /** صفحهٔ مینیمال فقط برای چاپ (بدون لایهٔ ادمین) */
    public function printSheet(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorize('view', $serviceOrder);

        $layout = $request->query('layout', 'full');
        $allowed = ['full', 'invoice', 'sale', 'proforma', 'receipt', 'delivery', 'mini'];
        if (! in_array($layout, $allowed, true)) {
            abort(404);
        }

        if (in_array($layout, ['invoice', 'sale', 'proforma'], true) && ! (auth()->user()->canManageAccounting() || auth()->user()->isReceptionist() || auth()->user()->isAdmin() || auth()->user()->canManageRepairs())) {
            abort(403, 'شما اجازه دسترسی به فاکتور را ندارید.');
        }

        $serviceOrder->load([
            'customer',
            'device',
            'technician',
            'repairItems' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
        ]);

        return view('service-orders.print-sheet', [
            'serviceOrder' => $serviceOrder,
            'layout' => $layout,
            'autoPrint' => $request->boolean('auto_print'),
        ]);
    }
}
