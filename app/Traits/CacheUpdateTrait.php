<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait CacheUpdateTrait {
    public static function bootCacheUpdateTrait()
    {
        $updateCache = function ($model) {
            $snakeModelName = strtolower(Str::snake(class_basename($model)));
            $cacheKey = $snakeModelName . '_count';
            Cache::forget($cacheKey);
        };

        static::created($updateCache);
        static::deleted($updateCache);
    }
}
