<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerInteraction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'customer_id',
        'user_id',
        'type',
        'content',
        'interaction_date',
    ];

    protected $casts = [
        'interaction_date' => 'datetime',
    ];

    /**
     * Get the customer that owns the interaction.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who recorded the interaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the type label in Persian.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'call' => 'تماس تلفنی',
            'meeting' => 'جلسه حضوری',
            'email' => 'ایمیل',
            'sms' => 'پیامک',
            'note' => 'یادداشت',
            default => 'سایر',
        };
    }

    /**
     * Get the type icon.
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'call' => 'ti-phone',
            'meeting' => 'ti-users',
            'email' => 'ti-mail',
            'sms' => 'ti-message',
            'note' => 'ti-note',
            default => 'ti-dots',
        };
    }
}
