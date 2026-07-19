<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'در انتظار پرداخت',
            self::PAID => 'پرداخت شده',
            self::FAILED => 'پرداخت ناموفق',
            self::REFUNDED => 'مرجوع شده',
        };
    }
}
