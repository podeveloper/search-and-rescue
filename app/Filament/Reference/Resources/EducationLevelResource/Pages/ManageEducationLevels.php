<?php

namespace App\Filament\Reference\Resources\EducationLevelResource\Pages;

use App\Filament\Reference\Resources\EducationLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEducationLevels extends ManageRecords
{
    protected static string $resource = EducationLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
