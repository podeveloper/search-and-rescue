<?php

namespace App\Filament\Reference\Resources\ContinentResource\Pages;

use App\Filament\Reference\Resources\ContinentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageContinents extends ManageRecords
{
    protected static string $resource = ContinentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
