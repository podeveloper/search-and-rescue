<?php

namespace App\Filament\Coordinator\Pages;

use App\Filament\Coordinator\Resources\UserResource;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Applicants extends ListRecords
{
    protected static string $resource = UserResource::class;

    public static ?string $title = 'Applicants';
    public static ?string $label = 'Applicants';
    public static ?string $navigationLabel = 'Applicants';
    protected static ?string $navigationIcon = 'heroicon-o-ellipsis-vertical';
    public static ?string $navigationGroup = 'Candidate Members';
    public static ?int $navigationSort = 9;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTableQuery(): ?Builder
    {
        return User::query()
            ->whereRole('applicant')
            ->orderBy('created_at','desc');
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
        $cacheKey = 'applicants_count';

        return Cache::rememberForever($cacheKey, function () {
            return User::query()->whereRole('applicant')->count();
        });
    }
}