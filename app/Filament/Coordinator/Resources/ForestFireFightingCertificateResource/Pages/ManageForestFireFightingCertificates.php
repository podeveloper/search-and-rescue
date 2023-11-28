<?php

namespace App\Filament\Coordinator\Resources\ForestFireFightingCertificateResource\Pages;

use App\Filament\Coordinator\Resources\ForestFireFightingCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageForestFireFightingCertificates extends ManageRecords
{
    protected static string $resource = ForestFireFightingCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
