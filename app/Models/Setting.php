<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'label',
        'type',
    ];

    /**
     * Get a setting value by key, or return default.
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function set(string $key, $value, array $attributes = [])
    {
        $existing = self::where('key', $key)->first();

        $payload = array_merge(['value' => $value], $attributes);

        if (! $existing && ! isset($payload['group'])) {
            $payload['group'] = self::defaultGroupForKey($key);
        }

        self::updateOrCreate(['key' => $key], $payload);
    }

    protected static function defaultGroupForKey(string $key): string
    {
        return match (true) {
            str_starts_with($key, 'two_factor') => 'security',
            str_starts_with($key, 'print_') => 'print_receipt',
            default => 'general',
        };
    }
}
