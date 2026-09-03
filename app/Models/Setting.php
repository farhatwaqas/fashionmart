<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $settings = Cache::rememberForever('app_settings', function () {
            return static::query()->pluck('value', 'key')->all();
        });

        return $settings[$key] ?? $default;
    }

    public static function setValue(string $key, mixed $value, string $group = 'general'): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('app_settings');
    }

    public static function flushCache(): void
    {
        Cache::forget('app_settings');
    }

    public static function logoUrl(): ?string
    {
        $path = static::getValue('store_logo');

        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
