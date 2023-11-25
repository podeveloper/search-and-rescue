<?php

namespace App\Filament\Coordinator\Resources\TodoCategoryResource\Pages;

use App\Filament\Coordinator\Resources\TodoCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTodoCategory extends CreateRecord
{
    protected static string $resource = TodoCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
