<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Support\ActivityLogFormatter;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loggable_type',
        'loggable_id',
        'event',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function eventLabel(): string
    {
        return ActivityLogFormatter::eventLabel($this->event);
    }

    public function modelLabel(): string
    {
        return ActivityLogFormatter::modelLabel($this->loggable_type);
    }

    public function summaryText(): string
    {
        return ActivityLogFormatter::summary(
            $this->event,
            $this->loggable_type,
            $this->loggable_id,
            $this->old_values,
            $this->new_values,
        );
    }

    /** @return list<string> */
    public function changeLines(): array
    {
        return ActivityLogFormatter::changeLines(
            $this->old_values,
            $this->new_values,
            $this->loggable_type,
        );
    }
}
