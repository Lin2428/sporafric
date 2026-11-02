<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Paramètres applicatifs clé / valeur (JSON).
 * AppSetting::get('rental.coefficient', 1.5) / AppSetting::put('rental.coefficient', 1.6)
 */
class AppSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = ['value' => 'json'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever("app_setting.{$key}", fn () => static::query()->where('key', $key)->first()?->value);

        return $value ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("app_setting.{$key}");
    }
}
