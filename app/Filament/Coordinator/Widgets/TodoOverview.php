<?php

namespace App\Filament\Coordinator\Widgets;

use App\Models\Todo;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodoOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Todos Finished', Todo::query()->where('is_finished', '=',true)->count()),
            Stat::make('Todos Unfinished', Todo::query()->where('is_finished', '=',false)->count()),
        ];
    }
}
