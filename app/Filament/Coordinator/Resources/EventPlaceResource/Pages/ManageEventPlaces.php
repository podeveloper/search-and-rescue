<?php

namespace App\Filament\Coordinator\Resources\EventPlaceResource\Pages;

use App\Filament\Coordinator\Resources\EventPlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEventPlaces extends ManageRecords
{
    protected static string $resource = EventPlaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
