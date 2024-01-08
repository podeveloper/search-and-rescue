<?php

namespace App\Filament\Coordinator\Pages;

use App\Filament\Coordinator\Resources\UserResource;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Trashed extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('general.trashed');
    }

    public static function getLabel(): ?string
    {
        return __('general.trashed');
    }

    public static function getNavigationLabel(): string
    {
        return __('general.trashed');
    }

    protected static ?string $navigationIcon = 'heroicon-o-ellipsis-vertical';
    public static ?string $navigationGroup = 'Human Resources';
    public static ?int $navigationSort = 3;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTableQuery(): ?Builder
    {
        return User::query()->onlyTrashed();
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All')
                ->badge($this->getTableQuery()->count())
                ->icon('heroicon-o-list-bullet'),
            'male' => Tab::make('Male')
                ->badge($this->getTableQuery()->male()->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->male()),
            'female' => Tab::make('Female')
                ->badge($this->getTableQuery()->female()->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->female()),
            'gender_null' => Tab::make('Gender Null')
                ->badge($this->getTableQuery()->genderNull()->count())
                ->badgeColor('danger')
                ->icon('heroicon-o-exclamation-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->genderNull()),
        ];

        return $tabs;
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = 'trashed_count';

        return Cache::rememberForever($cacheKey, function () {
            return User::query()->onlyTrashed()->count();
        });
    }
}
