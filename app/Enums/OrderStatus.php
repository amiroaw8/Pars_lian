<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'در انتظار بررسی',
            self::PROCESSING => 'در حال پردازش',
            self::SHIPPED => 'ارسال شده',
            self::DELIVERED => 'تحویل داده شده',
            self::CANCELLED => 'لغو شده',
        };
    }
}
