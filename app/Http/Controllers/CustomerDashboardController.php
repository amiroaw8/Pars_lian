<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\Order;
use App\Models\AccountingSale;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CustomerDashboardController extends Controller
{
    private function currentCustomer()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user?->customer;
    }

    private function repairOrdersQuery()
    {
        $customer = $this->currentCustomer();

        return ServiceOrder::query()
            ->when(
                $customer,
                fn ($query) => $query->where('customer_id', $customer->id),
                fn ($query) => $query->whereRaw('0 = 1')
            );
    }

    private function accountingSalesQuery()
    {
        $customer = $this->currentCustomer();

        return AccountingSale::query()
            ->when(
                $customer,
                fn ($query) => $query->where('customer_id', $customer->id),
                fn ($query) => $query->whereRaw('0 = 1')
            );
    }

    /**
     * Show customer dashboard
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) abort(401);
        
        $repairOrders = $this->repairOrdersQuery()
            ->with(['device', 'customer'])
            ->latest()
            ->take(5)
            ->get();

        // Find shop orders by user_id
        $shopOrders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->latest()
            ->take(5)
            ->get();

        // Get notifications (recent logs for their orders)
        $notifications = OrderLog::whereHas('serviceOrder', function ($query) {
            $customer = $this->currentCustomer();
            $query->where('customer_id', $customer?->id ?? 0);
        })->with('serviceOrder.device')->latest()->take(5)->get();

        // Financial summary
        $financials = $this->accountingSalesQuery()->select(
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(*) as total_invoices')
        )->first();

        $stats = [
            'total_repair_orders' => $this->repairOrdersQuery()->count(),
            'total_shop_orders' => Order::where('user_id', $user->id)->count(),
            'pending_repairs' => $this->repairOrdersQuery()->whereIn('status', ['registered', 'repairing'])->count(),
            'ready_repairs' => $this->repairOrdersQuery()->where('status', 'ready')->count(),
            'total_invoices' => $financials->total_invoices ?? 0,
            'total_payments' => $financials->total_amount ?? 0,
            'profile_completion' => $this->calculateProfileCompletion($user),
        ];

        return view('customer.dashboard', compact('repairOrders', 'shopOrders', 'stats', 'notifications'));
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($user)
    {
        $fields = [
            'first_name', 'last_name', 'phone', 'email',
            'province', 'city', 'street', 'plate', 'postal_code'
        ];
        
        $filledCount = 0;
        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $filledCount++;
            }
        }
        
        return round(($filledCount / count($fields)) * 100);
    }

    /**
     * Show customer invoices
     */
    public function invoices()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) abort(401);

        $invoices = $this->accountingSalesQuery()
            ->with('order.device')
            ->latest()
            ->paginate(10);

        return view('customer.invoices', compact('invoices'));
    }

    /**
     * Show customer orders
     */
    public function orders()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) abort(401);
        
        $repairOrders = $this->repairOrdersQuery()
            ->latest()
            ->paginate(10, ['*'], 'repair_page');

        // Shop orders
        $shopOrders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->latest()
            ->paginate(10, ['*'], 'shop_page');

        return view('customer.orders', compact('repairOrders', 'shopOrders'));
    }

    /**
     * Show single shop order details
     */
    public function showShopOrder(Order $order)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) abort(401);
        
        // Security check
        if ($order->user_id !== $user->id) {
            abort(403);
        }

        $order->load(['items.product', 'user']);
        
        return view('customer.shop-order-show', compact('order'));
    }

    /**
     * Show single order details
     */
    public function showOrder(ServiceOrder $serviceOrder)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) abort(401);
        
        if ((int) $serviceOrder->customer_id !== (int) ($user->customer?->id ?? 0)) {
            abort(403);
        }

        $serviceOrder->load([
            'repairItems',
            'customer',
            'device',
            'orderLogs' => fn ($q) => $q->orderBy('created_at'),
        ]);

        return view('customer.order-show', compact('serviceOrder'));
    }

    /**
     * Show profile edit form
     */
    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) abort(401);

        $customer = \App\Models\Customer::where('user_id', $user->id)->first();
        
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'is_current' => $session->id === session()->getId(),
                ];
            });

        return view('customer.profile', compact('user', 'customer', 'sessions'));
    }

    /**
     * Update customer profile
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'alley' => 'nullable|string|max:255',
            'plate' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = $request->first_name . ' ' . $request->last_name;
        $user->email = $request->email;
        
        $user->province = $request->province;
        $user->city = $request->city;
        $user->street = $request->street;
        $user->alley = $request->alley;
        $user->plate = $request->plate;
        $user->postal_code = $request->postal_code;
        
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        
        $user->save();
        
        // Sync with Customer record
        $customer = \App\Models\Customer::where('user_id', $user->id)->first();
            
        if ($customer) {
            $customer->update([
                'name' => $user->name,
                'address' => implode(' - ', array_filter([$user->province, $user->city, $user->street, $user->alley, $user->plate])),
                'user_id' => $user->id, // Ensure link is maintained
            ]);
        }

        return Redirect::back()->with('success', 'پروفایل با موفقیت بروزرسانی شد.');
    }
}
