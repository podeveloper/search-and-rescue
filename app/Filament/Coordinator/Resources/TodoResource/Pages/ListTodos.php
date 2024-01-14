<?php

namespace App\Filament\Coordinator\Resources\TodoResource\Pages;

use App\Filament\Coordinator\Resources\TodoResource;
use App\Models\Todo;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTodos extends ListRecords
{
    protected static string $resource = TodoResource::class;

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
                ->badge(Todo::query()->count())
                ->icon('heroicon-o-list-bullet'),
            'finished' => Tab::make('Finished')
                ->label(__('general.finished'))
                ->badge(Todo::query()->finished()->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn(Builder $query) => $query->finished()),
            'in_progress' => Tab::make('In Progress')
                ->label(__('general.in_progress'))
                ->badge(Todo::query()
                    ->unfinished()
                    ->inProgress()
                    ->count())
                ->badgeColor('warning')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->unfinished()
                    ->inProgress()
                ),
            'deadline_passed' => Tab::make('Deadline Passed')
                ->label(__('general.deadline_passed'))
                ->badge(Todo::query()
                    ->unfinished()
                    ->deadlinePassed()
                    ->count())
                ->badgeColor('danger')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->unfinished()
                    ->deadlinePassed()
                ),
            'assigned_to_me' => Tab::make('Assigned To Me')
                ->label(__('general.assigned_to_me'))
                ->badge(Todo::query()
                    ->assignedToMe()
                    ->count())
                ->badgeColor('primary')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->assignedToMe()
                ),
            'assigned_by_me' => Tab::make('Assigned By Me')
                ->label(__('general.assigned_by_me'))
                ->badge(Todo::query()
                    ->assignedByMe()
                    ->count())
                ->badgeColor('primary')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->assignedByMe()
                ),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}
