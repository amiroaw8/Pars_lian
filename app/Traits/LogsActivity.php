<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            $model->logActivity('created', null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $oldValues = array_intersect_key($model->getOriginal(), $model->getChanges());
            $newValues = $model->getChanges();
            
            if (!empty($newValues)) {
                $model->logActivity('updated', $oldValues, $newValues);
            }
        });

        static::deleted(function (Model $model) {
            $model->logActivity('deleted', $model->getAttributes(), null);
        });
    }

    public function logActivity(string $event, ?array $oldValues, ?array $newValues)
    {
        $userId = Auth::id();
        
        // Ensure user_id is null if it's 0 or empty string to prevent FK errors
        if (!$userId || $userId <= 0) {
            $userId = null;
        }

        ActivityLog::create([
            'user_id' => $userId,
            'loggable_type' => get_class($this),
            'loggable_id' => $this->id,
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable')->latest();
    }
}
