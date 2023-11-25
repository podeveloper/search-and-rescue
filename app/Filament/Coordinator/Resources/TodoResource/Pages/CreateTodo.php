<?php

namespace App\Filament\Coordinator\Resources\TodoResource\Pages;

use App\Filament\Coordinator\Resources\TodoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTodo extends CreateRecord
{
    protected static string $resource = TodoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
