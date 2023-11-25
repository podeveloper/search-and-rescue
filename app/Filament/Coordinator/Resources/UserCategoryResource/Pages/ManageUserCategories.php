<?php

namespace App\Filament\Coordinator\Resources\UserCategoryResource\Pages;

use App\Filament\Coordinator\Resources\UserCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageUserCategories extends ManageRecords
{
    protected static string $resource = UserCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
