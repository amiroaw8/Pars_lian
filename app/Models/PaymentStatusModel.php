<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentStatusModel extends Model
{
    protected $table = 'payment_statuses';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'label', 'sms_template', 'sms_enabled'];

    protected $casts = [
        'sms_enabled' => 'boolean',
    ];
}
