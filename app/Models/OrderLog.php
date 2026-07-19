<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Casts\Attribute;

class OrderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_order_id', 'user_id', 'action', 'changes', 
        'old_value', 'new_value', 'description', 
        'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    /**
     * Get the service order that this log belongs to
     */
    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human readable action name
     */
    protected function actionName(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->action) {
                'created' => 'ثبت سفارش',
                'updated' => 'ویرایش اطلاعات',
                'status_changed', 'status_change' => 'تغییر وضعیت',
                'attachment_added' => 'افزودن پیوست',
                'attachment_deleted' => 'حذف پیوست',
                'debt_recorded' => 'ثبت بدهی',
                'debt_settled' => 'تسویه بدهی',
                'technician_assigned' => 'تعیین تکنسین',
                default => $this->action,
            },
        );
    }

    /**
     * توضیح قابل نمایش برای مشتری (پنل کاربری)
     */
    public function customerDescription(): string
    {
        if (filled($this->description)) {
            return (string) $this->description;
        }

        if (in_array($this->action, ['status_change', 'status_changed'], true)) {
            $new = \App\Enums\ServiceOrderStatus::tryFrom((string) $this->new_value);
            $old = filled($this->old_value)
                ? \App\Enums\ServiceOrderStatus::tryFrom((string) $this->old_value)
                : null;

            if ($new) {
                if ($old) {
                    return "وضعیت از «{$old->label()}» به «{$new->label()}» تغییر کرد.";
                }

                return "وضعیت سفارش به «{$new->label()}» تغییر کرد.";
            }
        }

        return (string) $this->action_name;
    }
}
