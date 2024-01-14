<?php

namespace App\Filament\Coordinator\Resources\EventResource\Pages;

use App\Filament\Coordinator\Resources\EventResource;
use App\Models\Event;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->label(__('general.all'))
                ->badge(Event::query()->count())
                ->icon('heroicon-o-list-bullet')
                ->badgeColor('success'),
            'today' => Tab::make('Today')
                ->label(__('general.today'))
                ->badge(Event::query()->today()->count())
                ->icon('heroicon-o-list-bullet')
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->today()),
            'tomorrow' => Tab::make('Tomorrow')
                ->label(__('general.tomorrow'))
                ->badge(Event::query()->tomorrow()->count())
                ->icon('heroicon-o-list-bullet')
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->tomorrow()),
            'this_week' => Tab::make('This Week')
                ->label(__('general.this_week'))
                ->badge(Event::query()->thisWeek()->count())
                ->icon('heroicon-o-list-bullet')
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->thisWeek()),
            'published' => Tab::make('Published')
                ->label(__('general.published'))
                ->badge(Event::query()->published()->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->published()),
            'not_published' => Tab::make('Not Published')
                ->label(__('general.unpublished'))
                ->badge(Event::query()->notPublished()->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->notPublished()),
        ];
    }
}
