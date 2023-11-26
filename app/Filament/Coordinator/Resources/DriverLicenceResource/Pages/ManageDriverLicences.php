<?php

namespace App\Filament\Coordinator\Resources\DriverLicenceResource\Pages;

use App\Filament\Coordinator\Resources\DriverLicenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDriverLicences extends ManageRecords
{
    protected static string $resource = DriverLicenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
