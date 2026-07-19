<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Enums\ServiceOrderStatus;
use App\Services\ActiveWorkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Redirect users to their appropriate dashboard based on role.
     */
    public function index()
    {
        $user = Auth::user();

        if (!($user instanceof \App\Models\User)) {
            return to_route('login');
        }

        /** @var \App\Models\User $user */
        if ($user->isSuperAdmin()) {
            return to_route('super-admin.dashboard');
        }

        if ($user->isAdmin()) {
            return to_route('admin.dashboard');
        }

        if ($user->isEmployee()) {
            return to_route('automation.dashboard');
        }

        return to_route('customer.dashboard');
    }

    /**
     * Show the automation dashboard for staff.
     */
    public function automation(ActiveWorkService $activeWorkService)
    {
        $activeWorkSections = $activeWorkService->sectionsFor(Auth::user());

        return view('dashboard', [
            'activeWorkSections' => $activeWorkSections,
        ]);
    }

    /**
     * JSON feed for live active-work panel (polled from dashboard).
     */
    public function activeWorkJson(ActiveWorkService $activeWorkService)
    {
        $user = Auth::user();
        if (! ($user instanceof \App\Models\User)) {
            abort(401);
        }

        return response()
            ->json([
                'sections' => $activeWorkService->sectionsFor($user),
                'generated_at' => now()->toIso8601String(),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function receptionCartable()
    {
        $user = Auth::user();
        if (!($user instanceof \App\Models\User)) abort(401);
        /** @var \App\Models\User $user */

        if (!$user->isReceptionist() && !$user->isAdmin() && !$user->isSuperAdmin()) {
            abort(403, 'شما اجازه دسترسی به کارتابل پذیرش را ندارید.');
        }

        $orders = ServiceOrder::query()
            ->with(['customer', 'device', 'technician'])
            ->whereIn('status', [
                ServiceOrderStatus::REGISTERED,
                ServiceOrderStatus::READY,
                ServiceOrderStatus::REJECTED // Reception handles rejected devices too
            ])
            ->latest()
            ->paginate(20);

        return view('dashboard.cartable', [
            'title' => 'کارتابل پذیرش',
            'orders' => $orders,
            'type' => 'reception'
        ]);
    }

    public function repairCartable()
    {
        $user = Auth::user();
        if (!($user instanceof \App\Models\User)) abort(401);
        /** @var \App\Models\User $user */

        if (!$user->isTechnician() && !$user->isAdmin() && !$user->isSuperAdmin() && !$user->isReceptionist()) {
            abort(403, 'شما اجازه دسترسی به کارتابل تعمیرات را ندارید.');
        }

        $query = ServiceOrder::query()
            ->with(['customer', 'device', 'technician'])
            ->whereIn('status', [
                ServiceOrderStatus::REGISTERED,
                ServiceOrderStatus::TECHNICIAN_ASSIGNED,
                ServiceOrderStatus::REPAIRING,
                ServiceOrderStatus::PENDING_PARTS,
                ServiceOrderStatus::SENT_TO_WORKSHOP
            ]);

        if ($user->isTechnician() && ! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $query->where('technician_id', $user->id);
        }

        $orders = $query->latest()->paginate(20);

        return view('dashboard.cartable', [
            'title' => 'کارتابل تعمیرات',
            'orders' => $orders,
            'type' => 'repair'
        ]);
    }

    public function accountingCartable()
    {
        $user = Auth::user();
        if (!($user instanceof \App\Models\User)) abort(401);
        /** @var \App\Models\User $user */

        if (!$user->canManageAccounting()) {
            abort(403, 'شما اجازه دسترسی به کارتابل حسابداری را ندارید.');
        }

        $orders = ServiceOrder::query()
            ->with(['customer', 'device', 'technician', 'repairItems'])
            ->whereIn('status', [
                ServiceOrderStatus::ACCOUNTING
            ])
            ->latest()
            ->paginate(20);

        return view('dashboard.cartable', [
            'title' => 'کارتابل حسابداری',
            'orders' => $orders,
            'type' => 'accounting'
        ]);
    }

    public function deviceBank(Request $request)
    {
        $query = ServiceOrder::query()
            ->with(['customer', 'device', 'technician']);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('device', function($q) use ($search) {
                      $q->where('model', 'like', "%{$search}%")
                        ->orWhere('asset_number', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(20);

        return view('dashboard.cartable', [
            'title' => 'بانک دستگاه‌ها (همه سفارشات)',
            'orders' => $orders,
            'type' => 'bank'
        ]);
    }
}
