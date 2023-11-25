<?php

namespace App\Filament\Network\Resources\OrganisationResource\Pages;

use App\Filament\Network\Resources\OrganisationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganisation extends CreateRecord
{
    protected static string $resource = OrganisationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
