<?php

namespace App\Filament\Reference\Resources\LanguageResource\Pages;

use App\Filament\Reference\Resources\LanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLanguages extends ManageRecords
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
