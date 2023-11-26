<?php

namespace App\Filament\Coordinator\Resources\FirstAidCertificateResource\Pages;

use App\Filament\Coordinator\Resources\FirstAidCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFirstAidCertificates extends ManageRecords
{
    protected static string $resource = FirstAidCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
