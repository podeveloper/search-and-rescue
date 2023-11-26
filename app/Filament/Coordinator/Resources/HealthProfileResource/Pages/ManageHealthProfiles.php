<?php

namespace App\Filament\Coordinator\Resources\HealthProfileResource\Pages;

use App\Filament\Coordinator\Resources\HealthProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHealthProfiles extends ManageRecords
{
    protected static string $resource = HealthProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
