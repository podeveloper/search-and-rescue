<?php

namespace App\Filament\Stock\Resources\StockMovementResource\Pages;

use App\Filament\Stock\Resources\StockMovementResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStockMovements extends ManageRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
