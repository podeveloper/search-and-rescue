<?php

namespace App\Filament\Coordinator\Resources\UserResource\Pages;

use App\Filament\Coordinator\Resources\UserResource;
use App\Models\Tag;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All')
                ->badge(User::query()->count())
                ->icon('heroicon-o-list-bullet'),
            'male' => Tab::make('Male')
                ->badge(User::query()->male()->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->male()),
            'female' => Tab::make('Female')
                ->badge(User::query()->female()->count())
                ->badgeColor('success')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->female()),
            'gender_null' => Tab::make('Gender Null')
                ->badge(User::query()->genderNull()->count())
                ->badgeColor('danger')
                ->icon('heroicon-o-exclamation-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->genderNull()),
        ];

        // foreach (Tag::all() as $tag)
        // {
        //     $tabKey = strtolower(str_replace(' ','_',$tag));
        //     $tabs[$tabKey] = Tab::make($tag->name)
        //     ->badge(User::query()->whereTag($tag)->count())
        //     ->badgeColor('danger')
        //     ->icon('heroicon-o-exclamation-circle')
        //     ->modifyQueryUsing(fn (Builder $query) => $query->whereTag($tag));
        // }

        return $tabs;
    }
}
