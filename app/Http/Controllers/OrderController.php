<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderNote;
use App\Models\AccountingSale;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ! $user->isEmployee()) {
            abort(403);
        }

        $orders = $this->orderRepository->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function show(int $id)
    {
        $user = Auth::user();
        if (!$user || ! $user->isEmployee()) {
            abort(403);
        }

        $order = $this->orderRepository->find($id);
        
        if (!$order) {
            abort(404);
        }

        $order->load(['orderNotes.user:id,name', 'items.product']);

        return view('orders.show', compact('order'));
    }

    public function cancel(Request $request, int $id)
    {
        $user = Auth::user();
        if (! $user || (! $user->isAdmin() && ! $user->isSuperAdmin() && ! $user->isWarehouseManager() && ! $user->isReceptionist())) {
            abort(403, 'شما اجازه لغو سفارش را ندارید.');
        }

        $order = $this->orderRepository->find($id);
        if (! $order) {
            abort(404);
        }

        if (! $order->canBeCancelled()) {
            return Redirect::back()->with('error', 'این سفارش در وضعیت فعلی قابل لغو نیست.');
        }

        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->cancel();

        if (! empty(trim($validated['cancel_reason'] ?? ''))) {
            OrderNote::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'visibility' => 'internal',
                'body' => 'لغو سفارش: '.trim($validated['cancel_reason']),
            ]);
        }

        event(new \App\Events\OrderStatusChanged($order->fresh()));

        return Redirect::back()->with('success', 'سفارش '.hash_ref_plain($order->order_number).' لغو شد.');
    }

    public function storeNote(Request $request, int $id)
    {
        $user = Auth::user();
        if (! $user || ! $user->isEmployee()) {
            abort(403);
        }

        $order = $this->orderRepository->find($id);
        if (! $order) {
            abort(404);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'visibility' => ['required', 'in:internal,customer'],
        ]);

        OrderNote::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'visibility' => $validated['visibility'],
            'body' => trim($validated['body']),
        ]);

        return Redirect::back()->with('success', 'یادداشت ثبت شد.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || (! $user->isAdmin() && ! $user->isSuperAdmin() && ! $user->isWarehouseManager() && ! $user->isReceptionist())) {
            abort(403, 'شما اجازه تغییر وضعیت سفارشات فروشگاه را ندارید.');
        }

        $order = $this->orderRepository->find($id);
        
        if (!$order) {
            abort(404);
        }

        $statusValues = array_map(fn(OrderStatus $status) => $status->value, OrderStatus::cases());
        $paymentStatusValues = array_map(fn(PaymentStatus $status) => $status->value, PaymentStatus::cases());

        $request->validate([
            'status' => ['required', 'string', Rule::in($statusValues)],
            'payment_status' => ['nullable', 'string', Rule::in($paymentStatusValues)],
        ]);

        $oldStatus = $order->status->value;
        $oldPaymentStatus = $order->payment_status->value;

        try {
            DB::transaction(function () use ($id, $request) {
                $this->orderRepository->update($id, [
                    'status' => $request->status,
                ]);

                if ($request->has('payment_status')) {
                    $this->orderRepository->update($id, ['payment_status' => $request->payment_status]);
                }
            });
        } catch (\Throwable $e) {
            return Redirect::back()->with('error', 'خطا در بروزرسانی سفارش: ' . $e->getMessage());
        }

        $order->refresh();

        if ($oldStatus !== $request->status) {
            event(new \App\Events\OrderStatusChanged($order));
        }

        if ($request->has('payment_status') && $oldPaymentStatus !== $request->payment_status) {
            event(new \App\Events\PaymentStatusChanged($order));
        }

        return Redirect::back()->with('success', 'وضعیت سفارش با موفقیت بروزرسانی شد.');
    }

    public function updateTracking(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || (! $user->isAdmin() && ! $user->isSuperAdmin() && ! $user->isWarehouseManager() && ! $user->isReceptionist())) {
            abort(403, 'شما اجازه بروزرسانی اطلاعات پستی را ندارید.');
        }

        $order = $this->orderRepository->find($id);
        if (!$order) {
            abort(404);
        }

        $validated = $request->validate([
            'tracking_code' => 'nullable|string|max:100',
            'shipping_status' => 'nullable|string|max:100',
            'tracking_link' => 'nullable|string|max:500',
        ]);

        $trackingLink = trim((string) ($validated['tracking_link'] ?? ''));
        if ($trackingLink !== '' && ! preg_match('/^https?:\/\//i', $trackingLink)) {
            $trackingLink = 'https://'.$trackingLink;
        }

        if ($trackingLink !== '' && ! filter_var($trackingLink, FILTER_VALIDATE_URL)) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['tracking_link' => 'لینک رهگیری معتبر نیست.']);
        }

        $this->orderRepository->update($id, [
            'tracking_code' => $validated['tracking_code'] ?? null,
            'shipping_status' => $validated['shipping_status'] ?? null,
            'tracking_link' => $trackingLink !== '' ? $trackingLink : null,
        ]);

        return Redirect::back()->with('success', 'اطلاعات رهگیری پستی با موفقیت بروزرسانی شد.');
    }

    public function settleDebt(int $id)
    {
        $user = Auth::user();
        if (! $user || ! ($user->canAccessPos() || $user->isAdmin() || $user->isSuperAdmin())) {
            abort(403, 'شما اجازه تسویه بدهی این سفارش را ندارید.');
        }

        $order = $this->orderRepository->find($id);

        if (! $order) {
            abort(404);
        }

        if (! $order->hasOutstandingDebt()) {
            return Redirect::back()->with('error', 'این سفارش بدهی باز ندارد.');
        }

        try {
            DB::transaction(function () use ($order) {
                $this->orderRepository->update($order->id, [
                    'payment_status' => PaymentStatus::PAID,
                ]);

                $pendingSales = AccountingSale::query()
                    ->where('order_id', $order->id)
                    ->where('status', 'pending')
                    ->get();

                foreach ($pendingSales as $sale) {
                    $sale->update(['status' => 'cancelled']);

                    AccountingSale::create([
                        'customer_id' => $sale->customer_id,
                        'order_id' => $order->id,
                        'amount' => $sale->amount,
                        'description' => '[پرداخت] تسویه بدهی سفارش',
                        'transaction_date' => now(),
                        'payment_method' => 'cash',
                        'status' => 'completed',
                    ]);
                }

                if ($pendingSales->isEmpty()) {
                    AccountingSale::create([
                        'customer_id' => null,
                        'order_id' => $order->id,
                        'amount' => $order->total,
                        'description' => '[پرداخت] تسویه بدهی سفارش',
                        'transaction_date' => now(),
                        'payment_method' => 'cash',
                        'status' => 'completed',
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return Redirect::back()->with('error', 'خطا در تسویه بدهی: '.$e->getMessage());
        }

        $order->refresh();
        event(new \App\Events\PaymentStatusChanged($order));

        return Redirect::back()->with('success', 'بدهی سفارش '.hash_ref_plain($order->order_number).' به مبلغ '.number_format($order->total).' تومان تسویه شد.');
    }

    public function destroy(int $id)
    {
        $user = Auth::user();
        if (!$user || (! $user->isAdmin() && ! $user->isSuperAdmin())) {
            abort(403, 'فقط مدیران سیستم اجازه حذف سفارشات را دارند.');
        }

        $this->orderRepository->delete($id);
        return Redirect::route('automation.orders.index')->with('success', 'سفارش با موفقیت حذف شد.');
    }
}
