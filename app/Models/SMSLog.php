<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'message',
        'sms_id',
        'api_key_set',
        'error_code',
        'error_message',
        'provider_response',
        'service_order_id',
    ];

    protected function casts(): array
    {
        return [
            'api_key_set' => 'boolean',
            'provider_response' => 'array',
        ];
    }

    // اضافه کردن این خط برای اصلاح نام جدول
    protected $table = 'sms_logs';

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}
