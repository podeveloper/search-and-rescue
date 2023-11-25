<?php

namespace App\Filament\Reference\Resources\DistrictResource\Pages;

use App\Filament\Reference\Resources\DistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDistricts extends ManageRecords
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
