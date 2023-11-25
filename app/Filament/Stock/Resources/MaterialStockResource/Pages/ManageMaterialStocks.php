<?php

namespace App\Filament\Stock\Resources\MaterialStockResource\Pages;

use App\Filament\Stock\Resources\MaterialStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMaterialStocks extends ManageRecords
{
    protected static string $resource = MaterialStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
