<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = false;

public static function getValue($key, $default = null)
{
    try {
        // Cek dulu tabel settings ada gak
        if (!Schema::hasTable('settings')) {
            return $default;
        }

        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            return self::where('key', $key)->value('value') ?? $default;
        });
    } catch (QueryException $e) {
        // Jika error query, bisa return default agar gak crash
        return $default;
    }
}

}
