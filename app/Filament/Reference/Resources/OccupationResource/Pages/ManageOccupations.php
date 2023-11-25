<?php

namespace App\Filament\Reference\Resources\OccupationResource\Pages;

use App\Filament\Reference\Resources\OccupationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOccupations extends ManageRecords
{
    protected static string $resource = OccupationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
