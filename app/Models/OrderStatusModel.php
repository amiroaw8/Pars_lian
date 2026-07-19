<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusModel extends Model
{
    protected $table = 'order_statuses';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'label', 'sms_template', 'sms_enabled'];

    protected $casts = [
        'sms_enabled' => 'boolean',
    ];
}
