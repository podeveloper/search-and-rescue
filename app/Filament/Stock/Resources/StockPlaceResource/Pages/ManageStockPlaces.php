<?php

namespace App\Filament\Stock\Resources\StockPlaceResource\Pages;

use App\Filament\Stock\Resources\StockPlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStockPlaces extends ManageRecords
{
    protected static string $resource = StockPlaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
