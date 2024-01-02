<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait NavigationLocalizationTrait
{
    public static function getNavigationLabel(): string
    {
        $snakeModelName = strtolower(Str::snake(class_basename(self::getModel())));
        return __('general.'.$snakeModelName.'_plural');
    }

    public static function getModelLabel(): string
    {
        $snakeModelName = strtolower(Str::snake(class_basename(self::getModel())));
        return __('general.'.$snakeModelName.'_singular');
    }

    public static function getPluralModelLabel(): string
    {
        $snakeModelName = strtolower(Str::snake(class_basename(self::getModel())));
        return __('general.'.$snakeModelName.'_plural');
    }
}
