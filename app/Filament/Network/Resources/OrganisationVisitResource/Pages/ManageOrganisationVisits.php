<?php

namespace App\Filament\Network\Resources\OrganisationVisitResource\Pages;

use App\Filament\Network\Resources\OrganisationVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOrganisationVisits extends ManageRecords
{
    protected static string $resource = OrganisationVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
