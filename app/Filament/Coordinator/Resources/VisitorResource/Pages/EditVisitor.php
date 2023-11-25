<?php

namespace App\Filament\Coordinator\Resources\VisitorResource\Pages;

use App\Filament\Coordinator\Resources\VisitorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisitor extends EditRecord
{
    protected static string $resource = VisitorResource::class;

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
