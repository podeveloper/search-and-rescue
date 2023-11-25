<?php

namespace App\Filament\Coordinator\Resources\SocialAccountResource\Pages;

use App\Filament\Coordinator\Resources\SocialAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSocialAccounts extends ManageRecords
{
    protected static string $resource = SocialAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
