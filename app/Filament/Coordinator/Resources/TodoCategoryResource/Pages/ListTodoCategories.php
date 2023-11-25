<?php

namespace App\Filament\Coordinator\Resources\TodoCategoryResource\Pages;

use App\Filament\Coordinator\Resources\TodoCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTodoCategories extends ListRecords
{
    protected static string $resource = TodoCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
