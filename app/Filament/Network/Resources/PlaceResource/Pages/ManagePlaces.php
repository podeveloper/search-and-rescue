<?php

namespace App\Filament\Network\Resources\PlaceResource\Pages;

use App\Filament\Network\Resources\PlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePlaces extends ManageRecords
{
    protected static string $resource = PlaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
