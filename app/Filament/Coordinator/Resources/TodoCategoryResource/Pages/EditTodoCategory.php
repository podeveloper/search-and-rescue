<?php

namespace App\Filament\Coordinator\Resources\TodoCategoryResource\Pages;

use App\Filament\Coordinator\Resources\TodoCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTodoCategory extends EditRecord
{
    protected static string $resource = TodoCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
