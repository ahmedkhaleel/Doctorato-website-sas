<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /** Retrieve a single setting, cached. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("site_setting:{$key}", function () use ($key, $default) {
            $row = static::where('key', $key)->first();
            return $row?->value ?? $default;
        });
    }

    /** Create or update a setting and bust its cache. */
    public static function put(string $key, ?string $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget("site_setting:{$key}");
        Cache::forget('site_setting:group:' . $group);
    }

    /** All settings for a group, keyed by setting key. */
    public static function group(string $group): array
    {
        return Cache::rememberForever("site_setting:group:{$group}", function () use ($group) {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    protected static function booted(): void
    {
        static::saved(fn ($s) => Cache::forget("site_setting:{$s->key}") || Cache::forget('site_setting:group:' . $s->group));
        static::deleted(fn ($s) => Cache::forget("site_setting:{$s->key}") || Cache::forget('site_setting:group:' . $s->group));
    }
}
