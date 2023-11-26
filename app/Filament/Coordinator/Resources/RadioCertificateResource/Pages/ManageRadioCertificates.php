<?php

namespace App\Filament\Coordinator\Resources\RadioCertificateResource\Pages;

use App\Filament\Coordinator\Resources\RadioCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRadioCertificates extends ManageRecords
{
    protected static string $resource = RadioCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
