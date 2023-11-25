<?php

namespace App\Filament\Coordinator\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GenderStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Man Users', User::where('gender_id',1)->count()),
            Stat::make('Woman Users', User::where('gender_id',2)->count()),
            Stat::make('Gender Null', User::whereNull('gender_id')->count()),
            Stat::make('Total Users', User::count()),
            //->description('description')
            //->descriptionIcon('heroicon-m-arrow-trending-up')
            //->chart([7, 2, 10, 3, 15, 4, 17])
            //->color('success'),
        ];
    }
}
