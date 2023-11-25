<?php

namespace App\Filament\Reference\Resources\CertificateResource\Pages;

use App\Filament\Reference\Resources\CertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCertificate extends ViewRecord
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
