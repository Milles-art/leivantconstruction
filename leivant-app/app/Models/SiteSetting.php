<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type'];

    public static function values(): array
    {
        return Cache::remember('leivant.site_settings.values', 300, function (): array {
            return static::query()->pluck('value', 'key')->all();
        });
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::values()[$key] ?? $default;
    }

    public static function setValue(string $key, mixed $value, string $group = 'general', string $type = 'text'): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );

        Cache::forget('leivant.site_settings.values');
    }
}
