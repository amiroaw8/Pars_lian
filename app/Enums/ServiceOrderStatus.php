<?php

namespace App\Enums;

enum ServiceOrderStatus: string
{
    case REGISTERED = 'registered';
    case TECHNICIAN_ASSIGNED = 'technician_assigned';
    case REPAIRING = 'repairing';
    case PENDING_PARTS = 'pending_parts';
    case SENT_TO_WORKSHOP = 'sent_to_workshop';
    case REJECTED = 'rejected';
    case ACCOUNTING = 'accounting';
    case READY = 'ready';
    case DELIVERED = 'delivered';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::REGISTERED => 'ثبت شده',
            self::TECHNICIAN_ASSIGNED => 'تعیین تکنسین',
            self::REPAIRING => 'در حال تعمیر',
            self::PENDING_PARTS => 'منتظر قطعه',
            self::SENT_TO_WORKSHOP => 'ارسال به گارانتی/کارگاه',
            self::REJECTED => 'غیر قابل تعمیر',
            self::ACCOUNTING => 'در انتظار حسابداری',
            self::READY => 'آماده تحویل',
            self::DELIVERED => 'تحویل داده شده',
            self::ARCHIVED => 'بایگانی شده',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::REGISTERED => 'blue',
            self::TECHNICIAN_ASSIGNED => 'indigo',
            self::REPAIRING => 'yellow',
            self::PENDING_PARTS => 'orange',
            self::SENT_TO_WORKSHOP => 'purple',
            self::REJECTED => 'red',
            self::ACCOUNTING => 'orange',
            self::READY => 'green',
            self::DELIVERED => 'teal',
            self::ARCHIVED => 'gray',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::REGISTERED => 'clipboard-list',
            self::TECHNICIAN_ASSIGNED => 'user-cog',
            self::REPAIRING => 'tool',
            self::PENDING_PARTS => 'package',
            self::SENT_TO_WORKSHOP => 'truck-delivery',
            self::REJECTED => 'ban',
            self::ACCOUNTING => 'calculator',
            self::READY => 'package-export',
            self::DELIVERED => 'circle-check',
            self::ARCHIVED => 'archive',
        };
    }
}
