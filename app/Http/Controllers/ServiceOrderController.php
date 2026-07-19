<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Customer;
use App\Models\Device;
use App\Models\DeviceType;
use App\Models\OrderLog;
use App\Models\ServiceOrder;
use App\Services\OrderService;
use App\Services\RepairService;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests\ServiceOrderRequest;
use App\Http\Requests\UploadServiceOrderAttachmentRequest;
use App\Http\Requests\UpdateServiceOrderStatusRequest;
use App\Http\Requests\RepairRequest;
use App\Http\Requests\RepairItemRequest;
use App\Http\Requests\UpdateCostsRequest;
use App\Models\RepairItem;
use App\Enums\ServiceOrderStatus;

use App\Repositories\Interfaces\ServiceOrderRepositoryInterface;

use App\Models\Inventory;
use App\Models\User;
use App\Support\AssignableTechnicians;

class ServiceOrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly ServiceOrderRepositoryInterface $serviceOrderRepository,
        private readonly RepairService $repairService
    ) {
    }

    private function redirectToShow(
        ServiceOrder $serviceOrder,
        ?string $message = null,
        string $flash = 'success',
        ?string $scrollTo = null
    ): \Illuminate\Http\RedirectResponse {
        $redirect = Redirect::route('automation.service-orders.show', $serviceOrder);

        if ($message !== null) {
            $redirect = $redirect->with($flash, $message);
        }

        if ($scrollTo !== null && $scrollTo !== '') {
            $redirect = $redirect->with('scroll_to', $scrollTo);
        }

        return $redirect;
    }

    private function scrollToFromRequest(Request $request): ?string
    {
        $scrollTo = $request->input('scroll_to');

        return is_string($scrollTo) && $scrollTo !== '' ? $scrollTo : null;
    }

    private function authorizeManageRepair(ServiceOrder $serviceOrder): void
    {
        $this->authorize('manageRepair', $serviceOrder);
    }

    private function redirectBackWithScroll(
        Request $request,
        ?string $message = null,
        string $flash = 'success'
    ): \Illuminate\Http\RedirectResponse {
        $redirect = Redirect::back();

        if ($message !== null) {
            $redirect = $redirect->with($flash, $message);
        }

        if ($scrollTo = $this->scrollToFromRequest($request)) {
            $redirect = $redirect->with('scroll_to', $scrollTo);
        }

        return $redirect;
    }

    private function getUser(): User
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        return $user;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', ServiceOrder::class);

        $user = $this->getUser();
        $routeName = $request->route()?->getName();
        $viewType = $request->get('view');
        if ($viewType === null) {
            $viewType = $routeName === 'automation.repairs.index' ? 'repairs' : 'all';
        }
        $isRepairsList = $routeName === 'automation.repairs.index' || $viewType === 'repairs';

        $query = ServiceOrder::with(['customer', 'device', 'technician', 'statusModel']);

        // Apply filters based on view type and user role
        if ($viewType === 'my_repairs' && $user->isTechnician()) {
            $query->where('technician_id', $user->id)
                ->whereIn('status', [
                    ServiceOrderStatus::TECHNICIAN_ASSIGNED,
                    ServiceOrderStatus::REPAIRING,
                    ServiceOrderStatus::PENDING_PARTS,
                    ServiceOrderStatus::SENT_TO_WORKSHOP,
                    ServiceOrderStatus::READY,
                ]);
        } elseif ($viewType === 'financial' && ($user->isAccountant() || $user->isAdmin())) {
            $query->whereIn('status', [
                ServiceOrderStatus::ACCOUNTING,
                ServiceOrderStatus::READY,
                ServiceOrderStatus::DELIVERED
            ]);
        } elseif ($viewType === 'repairs') {
            $query->whereIn('status', [
                ServiceOrderStatus::REGISTERED,
                ServiceOrderStatus::TECHNICIAN_ASSIGNED,
                ServiceOrderStatus::REPAIRING,
                ServiceOrderStatus::PENDING_PARTS,
                ServiceOrderStatus::SENT_TO_WORKSHOP,
                ServiceOrderStatus::READY,
                ServiceOrderStatus::ACCOUNTING,
            ]);
        } elseif ($user->isTechnician() && ! $user->isAdmin() && ! $user->isReceptionist()) {
            $query->where('technician_id', $user->id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('device', function ($q) use ($search) {
                        $q->where('model', 'like', "%{$search}%")
                            ->orWhere('serial_number', 'like', "%{$search}%")
                            ->orWhere('asset_number', 'like', "%{$search}%");
                    });
            });
        }

        $serviceOrders = $query->latest()->paginate(20)->withQueryString();
        $statusCounts = $this->serviceOrderRepository->getStatusCounts();

        // Tab badge counts (independent of current page/filter pagination)
        $tabCounts = ['all' => $serviceOrders->total()];
        $myRepairsCount = 0;

        if ($user->isTechnician()) {
            $myRepairsCount = ServiceOrder::where('technician_id', $user->id)
                ->whereIn('status', [
                    ServiceOrderStatus::TECHNICIAN_ASSIGNED,
                    ServiceOrderStatus::REPAIRING,
                    ServiceOrderStatus::PENDING_PARTS,
                    ServiceOrderStatus::SENT_TO_WORKSHOP,
                ])
                ->count();

            $tabCounts['all'] = ServiceOrder::where('technician_id', $user->id)->count();
            $tabCounts['my_repairs'] = $myRepairsCount;
        }

        $financialCount = ServiceOrder::whereIn('status', [ServiceOrderStatus::ACCOUNTING, ServiceOrderStatus::READY])->count();

        return view('service-orders.index', compact('serviceOrders', 'statusCounts', 'myRepairsCount', 'financialCount', 'viewType', 'tabCounts', 'isRepairsList'));
    }

    public function destroy(int $id)
    {
        if (!$this->getUser()->isAdmin() && !$this->getUser()->isSuperAdmin()) {
            abort(403, 'فقط مدیران سیستم اجازه حذف سفارش را دارند.');
        }

        $this->serviceOrderRepository->delete($id);
        return Redirect::route('automation.service-orders.index')
            ->with('success', 'سفارش سرویس با موفقیت به سطل زباله منتقل شد.');
    }

    public function restore($id)
    {
        if (!$this->getUser()->isAdmin() && !$this->getUser()->isSuperAdmin()) {
            abort(403, 'فقط مدیران سیستم اجازه بازیابی سفارش را دارند.');
        }

        $serviceOrder = ServiceOrder::withTrashed()->findOrFail($id);
        $serviceOrder->restore();

        return Redirect::route('automation.service-orders.index', ['trashed' => 1])
            ->with('success', 'سفارش سرویس با موفقیت بازیابی شد.');
    }

    public function forceDelete($id)
    {
        if (!$this->getUser()->isSuperAdmin()) {
            abort(403, 'فقط سوپر ادمین اجازه حذف دائمی سفارش را دارد.');
        }

        $serviceOrder = ServiceOrder::withTrashed()->findOrFail($id);

        if ($serviceOrder->repairItems()->withTrashed()->exists() || $serviceOrder->attachments()->withTrashed()->exists() || $serviceOrder->orderLogs()->exists()) {
            return Redirect::back()->with('error', 'این سفارش دارای رکوردهای وابسته است و حذف دائمی آن مجاز نیست.');
        }

        $serviceOrder->forceDelete();

        return Redirect::route('automation.service-orders.index', ['trashed' => 1])
            ->with('success', 'سفارش سرویس برای همیشه حذف شد.');
    }

    public function export(Request $request, \App\Exports\ServiceOrderExport $export)
    {
        return $export->export($request);
    }

    public function create()
    {
        if (!$this->getUser()->canEditServiceOrders()) {
            abort(403, 'شما اجازه ثبت سفارش جدید را ندارید.');
        }

        $deviceTypes = Cache::remember('device_types_hierarchy', config('settings.device_types_cache_ttl', 3600), fn() => DeviceType::with('children')->whereNull('parent_id')->orderBy('name')->get());
        $newCustomer = session('new_customer_id');
        $preselectedCustomer = $newCustomer ? Customer::find($newCustomer) : null;
        $inventories = Inventory::active()->inStock()->get();
        $technicians = AssignableTechnicians::forSelect();

        return view('service-orders.create', compact('newCustomer', 'preselectedCustomer', 'deviceTypes', 'inventories', 'technicians'));
    }

    public function store(ServiceOrderRequest $request)
    {
        if (!$this->getUser()->canEditServiceOrders()) {
            abort(403, 'شما اجازه ثبت سفارش جدید را ندارید.');
        }

        $validated = $request->validated();
        if (! AssignableTechnicians::isAllowed((int) ($validated['technician_id'] ?? 0))) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['technician_id' => 'کاربر انتخاب‌شده در لیست تکنسین‌های مجاز نیست.']);
        }

        $order = $this->orderService->createOrder(
            $validated,
            $request->file('attachments')
        );

        // Handle initial inventory items if any
        if ($request->has('inventory_items') && is_array($request->inventory_items)) {
            foreach ($request->inventory_items as $itemData) {
                if (!empty($itemData['inventory_id'])) {
                    $inventory = Inventory::find($itemData['inventory_id']);
                    if ($inventory) {
                        $this->repairService->addItem($order, [
                            'item_type' => 'part',
                            'inventory_id' => $inventory->id,
                            'name' => $inventory->name,
                            'quantity' => $itemData['quantity'] ?? 1,
                            'cost' => $inventory->price,
                            'description' => $itemData['note'] ?? 'ثبت شده در هنگام پذیرش',
                        ]);
                    }
                }
            }
        }

        return Redirect::route('automation.service-orders.show', $order)
            ->with('success', 'سفارش سرویس با موفقیت ثبت شد.');
    }

    public function show(ServiceOrder $serviceOrder)
    {
        $this->authorize('view', $serviceOrder);

        // Auto-heal: technician assigned but status not updated (legacy mass-assignment bug)
        if ($serviceOrder->technician_id && $serviceOrder->status === ServiceOrderStatus::REGISTERED) {
            $serviceOrder->update(['status' => ServiceOrderStatus::TECHNICIAN_ASSIGNED]);
            $serviceOrder->refresh();
        }

        $serviceOrder->load([
            'customer',
            'device',
            'technician',
            'repairItems' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
            'repairItems.inventory',
            'attachments.uploader',
            'orderLogs.user',
        ]);

        $smsLogs = \App\Models\SMSLog::where('service_order_id', $serviceOrder->id)
            ->latest()
            ->get();

        $inventoryItems = Inventory::active()->inStock()->get();
        $technicians = AssignableTechnicians::forSelect($serviceOrder->technician_id);

        $repairItems = $serviceOrder->repairItems;

        return view('service-orders.show', [
            'serviceOrder' => $serviceOrder,
            'inventoryItems' => $inventoryItems,
            'technicians' => $technicians,
            'repairItems' => $repairItems,
            'smsLogs' => $smsLogs,
        ]);
    }

    public function storeAttachments(UploadServiceOrderAttachmentRequest $request, ServiceOrder $serviceOrder)
    {
        $this->authorize('uploadAttachment', $serviceOrder);

        $this->orderService->processAttachments($request->file('attachments'), $serviceOrder);

        return $this->redirectToShow(
            $serviceOrder,
            'فایل(ها) با موفقیت پیوست شد.',
            'success',
            $this->scrollToFromRequest($request) ?? 'attachments-section'
        );
    }

    public function edit(ServiceOrder $serviceOrder)
    {
        if (!$this->getUser()->canEditServiceOrders()) {
            abort(403, 'شما اجازه ویرایش سفارش را ندارید.');
        }

        // Allow editing for registered, ready, and delivered orders
        if (!$serviceOrder->canBeEdited()) {
            return Redirect::route('automation.service-orders.show', $serviceOrder)
                ->with('error', 'امکان ویرایش این سفارش در وضعیت فعلی وجود ندارد.');
        }

        $customers = Customer::orderBy('name')->get(['id', 'name', 'phone']);

        // For ready/delivered orders, don't allow device type changes
        $allowDeviceChanges = $serviceOrder->status === ServiceOrderStatus::REGISTERED;
        $deviceTypes = $allowDeviceChanges
            ? DeviceType::with('children')->whereNull('parent_id')->orderBy('name')->get()
            : collect();

        $technicians = AssignableTechnicians::forSelect($serviceOrder->technician_id);

        return view('service-orders.edit', compact('serviceOrder', 'customers', 'deviceTypes', 'technicians'));
    }

    public function update(ServiceOrderRequest $request, ServiceOrder $serviceOrder)
    {
        if ($request->boolean('debt_only')) {
            if (! $this->getUser()->canManageAccounting()) {
                abort(403, 'شما اجازه ثبت بدهی را ندارید.');
            }

            if (! $serviceOrder->canShowDebtSection()) {
                return Redirect::back()->with('error', 'امکان ثبت بدهی برای این سفارش در وضعیت فعلی وجود ندارد.');
            }

            $validated = $request->validated();
            $newDebt = (float) ($validated['debt_amount'] ?? 0);
            $previousDebt = (float) ($serviceOrder->debt_amount ?? 0);
            $totalDebt = $previousDebt + $newDebt;
            $reason = trim((string) ($validated['debt_reason'] ?? ''));

            $serviceOrder->update([
                'debt_amount' => $totalDebt,
                'debt_reason' => $reason !== '' ? $reason : $serviceOrder->debt_reason,
            ]);

            \App\Models\AccountingService::create([
                'service_order_id' => $serviceOrder->id,
                'technician_id' => $serviceOrder->technician_id,
                'amount' => $newDebt,
                'description' => '[بدهی] ' . ($reason !== '' ? $reason : 'ثبت بدهی جدید'),
                'transaction_date' => now(),
                'payment_status' => 'unpaid',
            ]);


            return Redirect::back()->with(
                'success',
                'بدهی ' . number_format($newDebt) . ' تومان ثبت شد. جمع بدهی فعلی: ' . number_format($totalDebt) . ' تومان.'
            );
        }

        if (!$this->getUser()->canEditServiceOrders()) {
            abort(403, 'شما اجازه ویرایش سفارش را ندارید.');
        }

        // Allow updating for registered, ready, and delivered orders
        if (!$serviceOrder->canBeEdited()) {
            return Redirect::route('automation.service-orders.show', $serviceOrder)
                ->with('error', 'امکان ویرایش این سفارش در وضعیت فعلی وجود ندارد.');
        }

        $validated = $request->validated();

        $this->orderService->updateOrder(
            $serviceOrder,
            $validated,
            $request->file('attachments')
        );

        return Redirect::route('automation.service-orders.index')
            ->with('success', 'سفارش سرویس با موفقیت ویرایش شد.');
    }

    public function updateStatus(UpdateServiceOrderStatusRequest $request, ServiceOrder $serviceOrder)
    {
        if (!$this->getUser()->canEditServiceOrders()) {
            abort(403, 'شما اجازه تغییر وضعیت سفارش را ندارید.');
        }

        try {
            $force = $request->boolean('force');
            $this->orderService->updateStatus($serviceOrder, $request->status, $request->note, $force);

            return Redirect::back()->with('success', 'وضعیت با موفقیت به روز شد.');
        } catch (\InvalidArgumentException $e) {
            return Redirect::back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'خطا در بروزرسانی وضعیت: ' . $e->getMessage());
        }
    }

    public function startRepair(ServiceOrder $serviceOrder)
    {
        if (!$this->getUser()->canManageRepairs()) {
            abort(403, 'شما اجازه شروع تعمیرات را ندارید.');
        }

        if (! $serviceOrder->technician_id) {
            return Redirect::back()->with('error', 'ابتدا باید تکنسین به این سفارش تخصیص داده شود.');
        }

        if ($serviceOrder->status !== ServiceOrderStatus::TECHNICIAN_ASSIGNED) {
            if ($serviceOrder->technician_id && $serviceOrder->status === ServiceOrderStatus::REGISTERED) {
                $this->orderService->updateStatus($serviceOrder, ServiceOrderStatus::TECHNICIAN_ASSIGNED, 'وضعیت به «تعیین تکنسین» اصلاح شد.', true);
                $serviceOrder->refresh();
            } else {
                return Redirect::back()->with('error', 'تعمیر فقط پس از تخصیص تکنسین و در وضعیت «تعیین تکنسین» قابل شروع است.');
            }
        }

        $user = $this->getUser();
        if ($user->isTechnician() && ! $user->isAdmin() && ! $user->isReceptionist() && (int) $serviceOrder->technician_id !== (int) $user->id) {
            return Redirect::back()->with('error', 'فقط تکنسین تخصیص‌یافته می‌تواند تعمیر را شروع کند.');
        }

        try {
            $this->orderService->updateStatus($serviceOrder, ServiceOrderStatus::REPAIRING, 'تعمیر دستگاه شروع شد.');

            return Redirect::route('automation.service-orders.show', $serviceOrder)
                ->with('success', 'تعمیر دستگاه شروع شد.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function assignTechnician(Request $request, ServiceOrder $serviceOrder)
    {
        $user = $this->getUser();
        if (! $user->isReceptionist() && ! $user->isAdmin() && ! $user->isSuperAdmin() && ! $user->canManageRepairs()) {
            abort(403, 'شما اجازه تعیین تکنسین را ندارید.');
        }

        $request->validate([
            'technician_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('users', 'id')
            ],
        ]);

        if (! AssignableTechnicians::isAllowed((int) $request->technician_id)) {
            return Redirect::back()->with('error', 'کاربر انتخاب‌شده در لیست تکنسین‌های مجاز نیست.');
        }

        try {
            $this->orderService->assignTechnician($serviceOrder, (int) $request->technician_id);
            return Redirect::back()->with('success', 'تکنسین با موفقیت تعیین شد.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function assignSelf(ServiceOrder $serviceOrder)
    {
        return Redirect::back()->with('error', 'تخصیص سفارش فقط توسط پذیرش در زمان ثبت یا ویرایش انجام می‌شود.');
    }

    public function updateRepair(RepairRequest $request, ServiceOrder $serviceOrder)
    {
        $this->authorizeManageRepair($serviceOrder);

        $serviceOrder->update($request->validated());

        if ($request->has('costs')) {
            $this->repairService->updateCosts($serviceOrder, $request->costs);
        }

        return Redirect::back()
            ->with('success', 'سفارش سرویس با موفقیت ویرایش شد.');
    }

    public function addRepairItem(RepairItemRequest $request, ServiceOrder $serviceOrder)
    {
        $this->authorizeManageRepair($serviceOrder);

        if (! $serviceOrder->canAddRepairItems()) {
            return Redirect::back()->with('error', 'امکان افزودن آیتم در وضعیت فعلی وجود ندارد.');
        }

        try {
            $data = $request->validated();
            if (($data['item_type'] ?? '') === 'service') {
                $data['item_type'] = 'labor';
            }
            $data['cost'] = isset($data['cost']) && $data['cost'] !== '' ? (float) $data['cost'] : 0.0;

            $this->repairService->addItem($serviceOrder, $data);

            return $this->redirectToShow(
                $serviceOrder,
                'آیتم با موفقیت اضافه شد.',
                'success',
                $this->scrollToFromRequest($request) ?? 'repair-items-section'
            );
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function reorderRepairItems(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorizeManageRepair($serviceOrder);

        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'integer|exists:repair_items,id',
        ]);

        $validIds = $serviceOrder->repairItems()->whereIn('id', $request->item_ids)->pluck('id')->all();
        if (count($validIds) !== count($request->item_ids)) {
            return response()->json(['message' => 'آیتم‌های نامعتبر'], 422);
        }

        $this->repairService->reorderItems($serviceOrder, $request->item_ids);

        return response()->json(['success' => true]);
    }

    public function recordDebt(Request $request, ServiceOrder $serviceOrder)
    {
        if (! $this->getUser()->canManageAccounting() && ! $this->getUser()->isAdmin()) {
            abort(403, 'شما اجازه ثبت بدهی را ندارید.');
        }

        if ($serviceOrder->status !== ServiceOrderStatus::ACCOUNTING) {
            return Redirect::back()->with('error', 'ثبت بدهی فقط در وضعیت «در انتظار حسابداری» امکان‌پذیر است.');
        }

        $request->validate([
            'debt_reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->repairService->recordDebt($serviceOrder, $request->input('debt_reason'));

            $freshOrder = $serviceOrder->fresh();
            $debtAmount = (float) ($freshOrder->debt_amount ?? 0);

            return Redirect::back()->with('success', 'بدهی مشتری با مبلغ ' . number_format($debtAmount) . ' تومان ثبت شد.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function removeRepairItem(Request $request, ServiceOrder $serviceOrder, RepairItem $repairItem)
    {
        $this->authorizeManageRepair($serviceOrder);

        if (! $serviceOrder->canBeEdited()) {
            return $this->redirectBackWithScroll($request, 'امکان حذف آیتم در وضعیت فعلی وجود ندارد.', 'error');
        }

        try {
            // Verify item belongs to order
            if ($repairItem->service_order_id !== $serviceOrder->id) {
                abort(404, 'آیتم مورد نظر یافت نشد.');
            }

            $this->repairService->removeItem($repairItem);

            return $this->redirectBackWithScroll($request, 'آیتم با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return $this->redirectBackWithScroll($request, $e->getMessage(), 'error');
        }
    }

    public function updateRepairItem(RepairItemRequest $request, ServiceOrder $serviceOrder, RepairItem $repairItem)
    {
        $this->authorizeManageRepair($serviceOrder);

        if (! $serviceOrder->canBeEdited()) {
            return $this->redirectToShow($serviceOrder, 'امکان ویرایش آیتم در وضعیت فعلی وجود ندارد.', 'error');
        }

        try {
            // Verify item belongs to order
            if ($repairItem->service_order_id !== $serviceOrder->id) {
                abort(404, 'آیتم مورد نظر یافت نشد.');
            }

            $this->repairService->updateItem($repairItem, $request->validated());

            return $this->redirectToShow(
                $serviceOrder,
                'آیتم با موفقیت ویرایش شد.',
                'success',
                $this->scrollToFromRequest($request) ?? 'repair-items-section'
            );
        } catch (\Exception $e) {
            return $this->redirectToShow(
                $serviceOrder,
                $e->getMessage(),
                'error',
                $this->scrollToFromRequest($request) ?? 'repair-items-section'
            );
        }
    }

    public function updateCosts(UpdateCostsRequest $request, ServiceOrder $serviceOrder)
    {
        $this->authorizeManageRepair($serviceOrder);

        try {
            $this->repairService->updateCosts($serviceOrder, $request->costs);
            return Redirect::back()->with('success', 'هزینه‌ها با موفقیت بروزرسانی شد.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function completeRepair(ServiceOrder $serviceOrder)
    {
        $this->authorizeManageRepair($serviceOrder);

        try {
            $this->repairService->completeRepair($serviceOrder);
            return Redirect::route('automation.service-orders.show', $serviceOrder)
                ->with('success', 'تعمیر با موفقیت تکمیل شد.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, ServiceOrder $serviceOrder)
    {
        if (!$this->getUser()->canManageRepairs()) {
            abort(403, 'شما اجازه رد سفارش را ندارید.');
        }

        $request->validate(['reason' => 'required|string|max:255']);

        try {
            $this->orderService->reject($serviceOrder, $request->reason);
            return Redirect::back()->with('success', 'سفارش با موفقیت رد شد.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function verifyPayment(Request $request, ServiceOrder $serviceOrder)
    {
        if (!$this->getUser()->canManageAccounting()) {
            abort(403, 'شما اجازه تایید پرداخت را ندارید.');
        }

        try {
            $costs = $request->input('costs', []);
            $taxPercent = $request->float('tax_percent', (float) (config('shop.tax_rate', 0.09) * 100));

            $this->repairService->verifyPayment($serviceOrder, $costs, $taxPercent);

            return Redirect::back()->with('success', 'فاکتور با موفقیت ثبت شد و دستگاه آماده تحویل است.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function settleDebt(Request $request, ServiceOrder $serviceOrder)
    {
        if (! $this->getUser()->canManageAccounting() && ! $this->getUser()->isAdmin()) {
            abort(403, 'شما اجازه تسویه بدهی را ندارید.');
        }

        $request->validate([
            'amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $amount = $request->filled('amount')
                ? (float) \App\Support\ShopFormat::toIntegerAmount($request->input('amount'))
                : null;

            $this->repairService->settleDebt($serviceOrder, $amount);
            $serviceOrder->refresh();

            $remaining = (float) ($serviceOrder->debt_amount ?? 0);
            $message = $remaining > 0
                ? 'بخشی از بدهی تسویه شد. مانده: ' . number_format($remaining) . ' تومان'
                : 'بدهی مشتری به‌طور کامل تسویه شد.';

            return Redirect::back()->with('success', $message);
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function deliver(ServiceOrder $serviceOrder)
    {
        if (!$this->getUser()->canEditServiceOrders()) {
            abort(403, 'شما اجازه تحویل سفارش را ندارید.');
        }

        try {
            $this->repairService->deliver($serviceOrder);
            return Redirect::back()->with('success', 'دستگاه تحویل مشتری شد.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export repair actions/items performed on a service order as CSV.
     */
    public function exportActions(ServiceOrder $serviceOrder)
    {
        $user = $this->getUser();

        // Allow admins, accountants, technicians (who manage repairs) and the customer owner
        $isOwner = $user->hasRole('customer') && $serviceOrder->customer_id === $user->customer?->id;
        if (! $user->canManageRepairs() && ! $user->canManageAccounting() && ! $user->canAccessAdminPanel() && ! $isOwner) {
            abort(403, 'شما اجازه دسترسی به این فایل را ندارید.');
        }

        $items = $serviceOrder->repairItems()->orderBy('created_at')->get();

        $filename = "service-order-{$serviceOrder->id}-actions.csv";

        $callback = function () use ($items) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['ID', 'نوع', 'نام', 'تعداد', 'هزینه واحد', 'جمع', 'توضیحات', 'تاریخ']);

            foreach ($items as $item) {
                $cost = (float) ($item->cost ?? 0);
                $total = $cost * (int) $item->quantity;
                fputcsv($out, [
                    $item->id,
                    $item->item_type,
                    $item->name,
                    $item->quantity,
                    $cost,
                    $total,
                    $item->description ?? '',
                    $item->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function archive(ServiceOrder $serviceOrder)
    {
        if (!$this->getUser()->canEditServiceOrders()) {
            abort(403, 'شما اجازه بایگانی سفارش را ندارید.');
        }

        try {
            $this->orderService->toArchived($serviceOrder);
            return Redirect::back()->with('success', 'سفارش با موفقیت بایگانی شد.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
}
