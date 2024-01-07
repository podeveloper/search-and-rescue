<?php

namespace App\Filament\Coordinator\Pages;

use App\Filament\Coordinator\Widgets\TodoCalendarWidget;
use App\Models\Todo;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class TodoCalendar extends Page
{
    protected static ?string $model = Todo::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.pages.event-calendar';

    protected static ?string $navigationGroup = 'Todo';

    protected static ?int $navigationSort = 97;

    protected function getHeaderWidgets(): array
    {
        return [
            TodoCalendarWidget::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = 'todo_count';
        $count = Cache::rememberForever($cacheKey, function () {
            return self::getModel()::count();
        });

        return $count;
    }

    public static function getModel(): string
    {
        return static::$model ?? '';
    }

    public static function getNavigationLabel(): string
    {
        return __('general.todo_calendar');
    }
}
