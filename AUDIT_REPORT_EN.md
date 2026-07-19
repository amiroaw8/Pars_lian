# Laravel Project Audit Report - Complete Findings

**Audit Date**: May 28, 2026  
**Project**: c:\laragon\laragon\www\pars-lian

---

## 🔴 CRITICAL ISSUE #1: SMS Duplicate Sending

### Location
**File**: `app/Providers/AppServiceProvider.php`  
**Lines**: 65-96

### Problem Description
Multiple event listeners are registered that can cause duplicate SMS messages. The `SendOrderSms` listener handles both `OrderStatusChanged` AND `PaymentStatusChanged` events, which can fire for the same order.

### Code Evidence
```php
// Line 80-84
Event::listen(
    OrderCreated::class,
    SendOrderSms::class,
);

// Line 85-89
Event::listen(
    OrderStatusChanged::class,
    SendOrderSms::class,
);

// Line 92-96 - PROBLEM HERE
Event::listen(
    PaymentStatusChanged::class,
    SendOrderSms::class,
);
```

### Related Files
- `app/Listeners/SendOrderSms.php` (lines 15-40)
- `app/Listeners/SendServiceOrderSms.php` (lines 23-40)
- `app/Jobs/SendSmsJob.php` (has duplicate detection on lines 36-48)

### Root Cause
When an order status changes AND payment status changes in the same transaction, SMS fires twice with the same message.

---

## 🔴 CRITICAL ISSUE #2: Undefined Array Keys

### Issue #2.1: StatusBadge Component
**File**: `app/View/Components/StatusBadge.php`  
**Line**: 35

```blade
$config = $statusLabels[$status] ?? ['label' => 'نامشخص', 'class' => 'status-unknown'];
```

**Problem**: If `$status` is undefined, uses fallback. Better to validate status before rendering.

### Issue #2.2: ServiceOrderDTO
**File**: `app/DTOs/ServiceOrderDTO.php`  
**Lines**: 27-40

```php
public static function fromRequest(array $validatedData): self
{
    return new self(
        customer_id: $validatedData['customer_id'],  // No check
        device_type: $validatedData['device_type'],   // No check
        device_model: $validatedData['device_model'],
        service_type: $validatedData['service_type'],
        receiver_name: $validatedData['receiver_name'],
        receiver_phone: $validatedData['receiver_phone'],
        fault: $validatedData['fault'],
        // Optional fields use ?? but required fields don't
    );
}
```

**Problem**: Required keys accessed without prior isset() verification if validation fails.

---

## 🔴 CRITICAL ISSUE #3: Services Without Price

### Location
**File**: `app/Models/AccountingService.php`  
**Lines**: 7-27

### The Problem
```php
protected $fillable = [
    'description',
    'service_order_id',
    'technician_id',
    'transaction_date',
    'payment_status',
    'tax_amount',
    // ⚠️ MISSING 'amount' !
];

protected function casts(): array
{
    return [
        'amount' => 'decimal:2',        // Line 27 - defined here
        'tax_amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];
}
```

### Impact
- The `amount` field is in `casts()` but NOT in `fillable[]`
- Cannot use mass assignment for amount
- Requires workaround using `forceFill()` in `AccountingManager::recordService()` (line 39)

### Related Usage
- `app/Services/AccountingManager.php` lines 30-48: Uses `forceFill(['amount' => $amount])`
- `app/Listeners/SyncServiceOrderToAccounting.php` lines 30-39: Calls recordService()

---

## 🔴 CRITICAL ISSUE #4: User Deletion Issues

### Issue #4.1: Soft Delete Without Validation
**File**: `app/Http/Controllers/Admin/UserManagementController.php`  
**Lines**: 100-113

```php
public function destroy(User $user)
{
    if ($user->isSuperAdmin() || $user->id === Auth::id()) {
        return redirect()->route('super-admin.users.index')
            ->with('error', 'نمی‌توانید این کاربر را حذف کنید.');
    }

    $user->delete();  // ✓ Soft delete only
}
```

**Problems**:
- No check if user has active service orders
- No check if user is technician with assigned repairs
- No check for pending payments or transactions

### Issue #4.2: Force Delete - Dangerous
**File**: `app/Http/Controllers/Admin/UserManagementController.php`  
**Lines**: 134-145

```php
public function forceDelete($id)
{
    $user = User::withTrashed()->findOrFail($id);
    
    if ($user->isSuperAdmin() || $user->id === Auth::id()) {
        return redirect()->route('super-admin.users.index', ['trashed' => 1])
            ->with('error', 'نمی‌توانید این کاربر را برای همیشه حذف کنید.');
    }

    $user->forceDelete();  // ⚠️ Permanent deletion
}
```

**Problems**:
- No cascade relationship validation
- Deletes all associated records
- No backup/archive mechanism

---

## 🟠 HIGH ISSUE #5: Password Reset Vulnerabilities

### Issue #5.1: No Rate Limiting on SMS
**File**: `app/Http/Controllers/Auth/ForgotPasswordController.php`  
**Lines**: 20-46

```php
public function sendResetCode(Request $request)
{
    $request->validate(['phone' => 'required|digits:11|starts_with:09']);

    $user = User::where('phone', $request->phone)->first();
    if (!$user) {
        return back()->withInput()->withErrors([...]);
    }

    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    PasswordReset::updateOrCreate(
        ['phone' => $request->phone],
        ['code' => $code, 'expires_at' => now()->addMinutes(15)]  // ⚠️ No rate limit
    );

    try {
        $this->smsService->sendSMS($request->phone, "...");  // Can send unlimited times
        // ...
    }
}
```

**Problems**:
- User can request unlimited reset codes
- No rate limiting on SMS sending
- `password_resets` table grows without cleanup

### Issue #5.2: Incomplete Token Validation
**File**: `app/Http/Controllers/Auth/ResetPasswordController.php`  
**Lines**: 26-39

```php
public function showResetForm(Request $request, $token = null)
{
    $reset = PasswordReset::where('reset_token', $token)
        ->where('verified_at', '!=', null)
        ->first();

    if (!$reset) {
        abort(404, 'لینک بازیابی نامعتبر یا منقضی است.');
    }
    // ⚠️ Doesn't check expiration time
}
```

---

## 🟠 HIGH ISSUE #6: Product Image Handling Problems

### Issue #6.1: No Image Validation on Upload
**File**: `app/Http/Controllers/Admin/ProductManagementController.php`  
**Lines**: 62-69

```php
$imagePaths = [];
if ($request->hasFile('images')) {
    foreach ($request->file('images') as $image) {
        $path = $image->store('products', 'public');  // ⚠️ No validation
        $imagePaths[] = $path;
    }
}
$productData['images'] = $imagePaths;
```

**Problems**:
- No extension validation (can upload executables)
- No MIME type check
- No file size limit
- No maximum image count

### Issue #6.2: Image Array Accumulation
**File**: `app/Http/Controllers/Admin/ProductManagementController.php`  
**Lines**: 95-99

```php
if ($request->hasFile('images')) {
    $imagePaths = $product->images ?? [];  // Gets existing images
    foreach ($request->file('images') as $image) {
        $path = $image->store('products', 'public');
        $imagePaths[] = $path;  // ⚠️ Only adds, never removes old ones
    }
    $productData['images'] = $imagePaths;
}
```

**Problem**: Images array grows indefinitely. Old images never deleted from storage.

### Issue #6.3: Force Delete Image Cleanup Issue
**File**: `app/Http/Controllers/Admin/ProductManagementController.php`  
**Lines**: 125-143

```php
public function forceDelete($id)
{
    $product = Product::withTrashed()->findOrFail($id);
    
    if ($product->images) {
        foreach ($product->images as $imageUrl) {
            $path = $imageUrl;
            if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                $urlPath = parse_url($imageUrl, PHP_URL_PATH);
                $path = preg_replace('#^/storage/#', '', $urlPath);
            } else {
                $path = preg_replace('#^/storage/#', '', $imageUrl);
                $path = ltrim($path, '/');
            }
            Storage::disk('public')->delete($path);  // ⚠️ No error handling
        }
    }
    
    $product->forceDelete();  // If image delete fails, still proceeds
}
```

**Problems**:
- No try-catch
- If image path is corrupted, exception thrown
- No transaction rollback

---

## 🟠 HIGH ISSUE #7: Customer Deletion Issues

### Problem
**File**: `app/Http/Controllers/CustomerController.php`  
**Lines**: 289-306 (soft delete) and 323-334 (force delete)

```php
public function destroy(Customer $customer)
{
    // Only checks service orders
    if ($customer->serviceOrders()->whereNotIn('status', ['completed', 'cancelled'])->exists()) {
        return Redirect::back()->with('error', '...');
    }

    $customer->delete();  // Soft delete
}

public function forceDelete($id)
{
    $customer = Customer::withTrashed()->findOrFail($id);
    // ⚠️ No validation before permanent delete
    $customer->forceDelete();
}
```

**Issues**:
- Shop orders not checked
- Customer interactions deleted permanently
- Payment history lost
- No archive mechanism

---

## 🟡 MEDIUM ISSUE #8: Inventory & Order Problems

### Issue #8.1: Services Registered Without Cost
**File**: `app/Services/RepairService.php`  
**Lines**: 15-45

```php
public function addItem(ServiceOrder $order, array $data): RepairItem
{
    return DB::transaction(function () use ($order, $data) {
        $cost = $data['cost'] ?? 0;  // Default to 0!

        if ($order->is_warranty) {
            $cost = 0;
        }

        $item = $order->repairItems()->create($data + [
            'cost' => $cost,
            'sort_order' => $order->repairItems()->count(),
        ]);

        // Stock reduced even if cost is 0
        if ($item->inventory_id) {
            $item->inventory->updateStock(
                -$item->quantity,
                'use',
                "استفاده در تعمیر - سفارش #{$order->id}"
            );
        }

        $order->recalculateServiceCost();
    });
}
```

**Problems**:
- Items added with $cost = 0 by default
- Stock reduced but no revenue
- Service cost might be incorrect

### Issue #8.2: Incomplete Service Cost Calculation
**File**: `app/Models/ServiceOrder.php`  
**Lines**: 155-170

```php
public function recalculateServiceCost(): void
{
    $this->service_cost = $this->repairItems()->sum(DB::raw('cost * quantity'));
    $this->save();
    // ⚠️ Only sums cost * quantity, doesn't include tax/discounts
}
```

**Problem**: 
- Service cost only includes repair items
- Technician service fees in `AccountingService` calculated separately
- Total cost calculation incomplete

### Issue #8.3: Stock Management Race Conditions
**File**: `app/Models/Inventory.php`  
**Lines**: 87-110

```php
public function updateStock(int $quantityChange, string $transactionType, string $notes = '', array $details = []): bool
{
    return DB::transaction(function () use ($quantityChange, $transactionType, $notes, $details) {
        $inventory = self::where('id', $this->id)->lockForUpdate()->first();
        // ...
    });
}
```

**Positives**: Uses `lockForUpdate()` for transaction safety

**But Missing**:
- No minimum quantity enforcement
- No low stock warnings
- No reorder point validation

---

## 🟡 MEDIUM ISSUE #9: User Roles & Permissions

### Location
**File**: `app/Models/User.php`  
**Lines**: 11-126

### Current Implementation ✓
```php
use Spatie\Permission\Traits\HasRoles;

// Multiple role checking methods defined
public function isSuperAdmin(): bool { return $this->hasRole('super_admin'); }
public function isAdmin(): bool { return $this->hasRole('admin'); }
public function isTechnician(): bool { return $this->hasRole('technician'); }
// ... etc
```

### Problem: Flawed Customer Role Logic
**Line**: 118

```php
public function isCustomer(): bool
{
    return $this->hasRole('customer') || $this->roles->isEmpty();  // ⚠️ WRONG
}
```

**Problem**: If a user has NO roles assigned, they're treated as customer! Should be:
```php
return $this->hasRole('customer');  // Only explicit customer role
```

### Missing
- No middleware enforcing permissions
- No permission checks in controllers
- No role-based middleware

---

## 🟡 MEDIUM ISSUE #10: SMS Service Configuration

### Location
**File**: `app/Services/SMSService.php`  
**Lines**: 13-22

```php
protected readonly string $apiKey;

public function __construct(
    ?string $apiKey = null,
    ?string $lineNumber = null,
    ?string $baseUrl = null
) {
    $this->apiKey = $apiKey ?? (string) config('services.smsir.api_key', '');
    // ⚠️ Falls back to empty string, not false
}
```

### Problems
1. **Line 22**: Returns empty string if config not set, not clear failure signal
2. **Line 69**: Checks `empty($this->apiKey)` which works but is implicit

### Positive Notes
- **Lines 36-48** in `SendSmsJob`: Has duplicate detection mechanism
- **Lines 69-70**: Gracefully handles missing API key with logging

---

## 📊 Summary Table

| Priority | Issue | File | Lines | Type |
|----------|-------|------|-------|------|
| 🔴 CRITICAL | SMS Duplicate Sending | AppServiceProvider.php | 80-96 | Event Listener |
| 🔴 CRITICAL | Undefined Array Keys | StatusBadge.php | 35 | Undefined Index |
| 🔴 CRITICAL | Service without Amount | AccountingService.php | 7-27 | Model Fillable |
| 🔴 CRITICAL | User Force Delete | UserManagementController.php | 134-145 | Security |
| 🟠 HIGH | Product Image Accumulation | ProductManagementController.php | 95-99 | Storage |
| 🟠 HIGH | Password Reset Limits | ForgotPasswordController.php | 20-46 | Rate Limiting |
| 🟠 HIGH | Customer Deletion | CustomerController.php | 289-334 | Validation |
| 🟡 MEDIUM | Service Zero Cost | RepairService.php | 15-45 | Business Logic |
| 🟡 MEDIUM | Role Logic Error | User.php | 118 | Permission |
| 🟡 MEDIUM | SMS Config | SMSService.php | 13-22 | Configuration |

---

## ✅ Files Created

1. **AUDIT_REPORT.md** (Persian, detailed)
2. **AUDIT_REPORT_EN.md** (English, this file)

Both files are located at project root: `c:\laragon\laragon\www\pars-lian\`

