<?php

namespace App\Filament\Stock\Resources\MaterialCategoryResource\Pages;

use App\Filament\Stock\Resources\MaterialCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMaterialCategories extends ManageRecords
{
    protected static string $resource = MaterialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
