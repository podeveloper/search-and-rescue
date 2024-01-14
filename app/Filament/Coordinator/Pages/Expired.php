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

class Expired extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('general.expired');
    }

    /**
     * @return string|null
     */
    public static function getLabel(): ?string
    {
        return __('general.expired');
    }

    public static function getNavigationLabel(): string
    {
        return __('general.expired');
    }

    protected static ?string $navigationIcon = 'heroicon-o-ellipsis-vertical';
    public static ?string $navigationGroup = 'Applicants';
    public static ?int $navigationSort = 10;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTableQuery(): ?Builder
    {
        return User::query()->expired();
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All')
                ->label(__('general.all'))
                ->badge($this->getTableQuery()->count())
                ->icon('heroicon-o-list-bullet'),
            'male' => Tab::make('Male')
                ->label(__('general.male'))
                ->badge($this->getTableQuery()->male()->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->male()),
            'female' => Tab::make('Female')
                ->label(__('general.female'))
                ->badge($this->getTableQuery()->female()->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->female()),
            'gender_null' => Tab::make('Gender Null')
                ->label(__('general.gender_null'))
                ->badge($this->getTableQuery()->genderNull()->count())
                ->badgeColor('danger')
                ->icon('heroicon-o-exclamation-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->genderNull()),
        ];

        return $tabs;
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = 'expired_count';

        return Cache::rememberForever($cacheKey, function () {
            return User::query()->expired()->count();
        });
    }
}
