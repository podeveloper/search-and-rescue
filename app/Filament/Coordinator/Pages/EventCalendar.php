<?php

namespace App\Filament\Coordinator\Pages;

use App\Filament\Coordinator\Widgets\EventCalendarWidget;
use App\Models\Event;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class EventCalendar extends Page
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.pages.event-calendar';

    protected static ?string $navigationGroup = 'Events';

    protected static ?int $navigationSort = 18;

    protected function getHeaderWidgets(): array
    {
        return [
            EventCalendarWidget::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = 'event_count';
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
        return __('event.calendar');
    }
}
