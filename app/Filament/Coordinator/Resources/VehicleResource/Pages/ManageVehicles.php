<?php

namespace App\Filament\Coordinator\Resources\VehicleResource\Pages;

use App\Filament\Coordinator\Resources\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVehicles extends ManageRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
