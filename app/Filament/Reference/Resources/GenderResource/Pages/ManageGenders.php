<?php

namespace App\Filament\Reference\Resources\GenderResource\Pages;

use App\Filament\Reference\Resources\GenderResource;
use Filament\Resources\Pages\ManageRecords;

class ManageGenders extends ManageRecords
{
    protected static string $resource = GenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
