<?php

namespace App\Filament\Network\Resources\VisitorResource\Pages;

use App\Filament\Network\Resources\VisitorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVisitors extends ListRecords
{
    protected static string $resource = VisitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
