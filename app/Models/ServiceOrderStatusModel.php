<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrderStatusModel extends Model
{
    protected $table = 'service_order_statuses';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'label', 'color', 'sms_template', 'sms_enabled'];

    protected function casts(): array
    {
        return [
            'sms_enabled' => 'boolean',
        ];
    }
}
