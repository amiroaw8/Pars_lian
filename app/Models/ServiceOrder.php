<?php

namespace App\Models;

use App\Enums\ServiceOrderStatus;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ServiceOrder extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'customer_id', 'device_id', 'service_type', 'is_warranty', 'warranty_id',
        'receiver_name', 'receiver_phone', 'user_department', 
        'accessories', 'fault', 'notes', 'technician_id', 'status',
        'repair_steps', 'used_parts', 
        'repair_started_at', 'repair_completed_at',
        'debt_amount', 'debt_reason',
    ];

    protected $casts = [
        'status' => ServiceOrderStatus::class,
        'is_warranty' => 'boolean',
        'repair_started_at' => 'datetime',
        'repair_completed_at' => 'datetime',
        'debt_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function statusModel(): BelongsTo
    {
        return $this->belongsTo(ServiceOrderStatusModel::class, 'status', 'id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function repairItems(): HasMany
    {
        return $this->hasMany(RepairItem::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SMSLog::class);
    }

    public function orderLogs(): HasMany
    {
        return $this->hasMany(OrderLog::class);
    }

    public function accountingServices(): HasMany
    {
        return $this->hasMany(AccountingService::class);
    }

    public function accountingSales(): HasMany
    {
        return $this->hasMany(AccountingSale::class, 'order_id');
    }

    public function shopOrders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the status label.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => $this->status->label());
    }

    /**
     * Get the status color.
     */
    protected function statusColor(): Attribute
    {
        return Attribute::get(fn () => $this->status->color());
    }

    /**
     * Get the service order number.
     */
    protected function orderNumber(): Attribute
    {
        return Attribute::get(fn () => 'SRV-' . str_pad($this->id, 5, '0', STR_PAD_LEFT));
    }

    /**
     * Get the total cost (alias for service_cost).
     */
    protected function totalCost(): Attribute
    {
        return Attribute::get(fn () => (float) ($this->service_cost ?? 0));
    }

    /**
     * Generate status message for SMS
     */
    public function getStatusSmsMessage(?string $statusId = null): string
    {
        $statusId = $statusId ?? $this->status->value;
        $statusModel = ServiceOrderStatusModel::find($statusId);
        
        if (!$statusModel || !$statusModel->sms_template) {
            return 'وضعیت دستگاه شما به روز شد.';
        }

        return str_replace('{id}', $this->id, $statusModel->sms_template);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if the service order can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, [
            ServiceOrderStatus::REGISTERED,
            ServiceOrderStatus::TECHNICIAN_ASSIGNED,
            ServiceOrderStatus::REPAIRING,
            ServiceOrderStatus::PENDING_PARTS,
            ServiceOrderStatus::SENT_TO_WORKSHOP,
            ServiceOrderStatus::ACCOUNTING,
        ]);
    }

    /**
     * آیا فرایند تعمیر شروع شده است؟
     */
    public function hasRepairStarted(): bool
    {
        if ($this->repair_started_at) {
            return true;
        }

        return in_array($this->status, [
            ServiceOrderStatus::REPAIRING,
            ServiceOrderStatus::PENDING_PARTS,
            ServiceOrderStatus::SENT_TO_WORKSHOP,
            ServiceOrderStatus::ACCOUNTING,
            ServiceOrderStatus::READY,
            ServiceOrderStatus::DELIVERED,
            ServiceOrderStatus::REJECTED,
            ServiceOrderStatus::ARCHIVED,
        ], true);
    }

    /**
     * نمایش بخش ثبت/مدیریت بدهی (پس از اتمام تعمیر تا تسویه).
     */
    public function canShowDebtSection(): bool
    {
        if ((float) ($this->debt_amount ?? 0) > 0) {
            return true;
        }

        return in_array($this->status, [
            ServiceOrderStatus::ACCOUNTING,
            ServiceOrderStatus::READY,
            ServiceOrderStatus::DELIVERED,
        ], true);
    }

    /**
     * افزودن قطعه/خدمات از شروع تعمیر تا قبل از ثبت بدهی یا تایید پرداخت.
     */
    public function canAddRepairItems(): bool
    {
        if (! $this->hasRepairStarted()) {
            return false;
        }

        if ((float) ($this->debt_amount ?? 0) > 0) {
            return false;
        }

        return ! in_array($this->status, [
            ServiceOrderStatus::READY,
            ServiceOrderStatus::DELIVERED,
            ServiceOrderStatus::ARCHIVED,
            ServiceOrderStatus::REJECTED,
        ], true);
    }

    /**
     * Recalculate and update the total service cost.
     */
    public function recalculateServiceCost(): void
    {
        $this->service_cost = $this->repairItems()->sum(DB::raw('cost * quantity'));
        $this->save();
    }

    /**
     * Get the calculated total cost of all repair items.
     */
    protected function calculatedServiceCost(): Attribute
    {
        return Attribute::get(function () {
            return $this->repairItems->sum(function ($item) {
                return $item->cost * $item->quantity;
            });
        });
    }
}
