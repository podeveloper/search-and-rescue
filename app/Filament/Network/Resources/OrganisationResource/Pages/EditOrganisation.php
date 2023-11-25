<?php

namespace App\Filament\Network\Resources\OrganisationResource\Pages;

use App\Filament\Network\Resources\OrganisationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganisation extends EditRecord
{
    protected static string $resource = OrganisationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
