<?php

namespace App\Filament\Network\Resources\ContactCategoryResource\Pages;

use App\Filament\Network\Resources\ContactCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageContactCategories extends ManageRecords
{
    protected static string $resource = ContactCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
