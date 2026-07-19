<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Support\ShopFormat;

class ProformaInvoiceController extends Controller
{
    private const CACHE_PREFIX = 'proforma_print:';

    public function create()
    {
        if (! Auth::user()?->canManageAccounting()) {
            abort(403);
        }

        return view('accounting.proforma.create');
    }

    public function redirectToCreate()
    {
        if (! Auth::user()?->canManageAccounting()) {
            abort(403);
        }

        return redirect()->route('automation.accounting.proforma.create');
    }

    public function store(Request $request)
    {
        if (! Auth::user()?->canManageAccounting()) {
            abort(403);
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

        $payload = $this->buildPayload($validated, $request);
        $token = Str::random(40);

        Cache::put(self::CACHE_PREFIX.$token, $payload, now()->addHours(2));

        return redirect()->route('automation.accounting.proforma.print.show', ['token' => $token]);
    }

    public function show(string $token)
    {
        if (! Auth::user()?->canManageAccounting()) {
            abort(403);
        }

        $payload = Cache::get(self::CACHE_PREFIX.$token);

        if (! $payload) {
            return redirect()
                ->route('automation.accounting.proforma.create')
                ->with('error', 'اطلاعات پیش‌فاکتور منقضی یا یافت نشد. لطفاً دوباره فرم را تکمیل کنید.');
        }

        return view('accounting.proforma.print', $payload);
    }

    private function buildPayload(array $validated, Request $request): array
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
}
