<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\LogsActivity;

class Attachment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'path', 'mime_type', 'size', 
        'attachable_type', 'attachable_id', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    /**
     * Get the attachable model (polymorphic relationship)
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    /**
     * Get the user who uploaded the file
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get human readable file size
     */
    protected function humanReadableSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                $bytes = $this->size;
                $units = ['B', 'KB', 'MB', 'GB'];

                for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                    $bytes /= 1024;
                }

                return round($bytes, 2).' '.$units[$i];
            }
        );
    }

    /**
     * Get file extension
     */
    protected function extension(): Attribute
    {
        return Attribute::make(
            get: fn () => pathinfo($this->name, PATHINFO_EXTENSION),
        );
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function previewUrl(): ?string
    {
        if (! $this->isImage()) {
            return null;
        }

        return route('automation.attachments.preview', $this);
    }
}
