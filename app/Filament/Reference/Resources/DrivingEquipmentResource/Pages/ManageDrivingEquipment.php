<?php

namespace App\Filament\Reference\Resources\DrivingEquipmentResource\Pages;

use App\Filament\Reference\Resources\DrivingEquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDrivingEquipment extends ManageRecords
{
    protected static string $resource = DrivingEquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
