<?php

namespace App\Filament\Coordinator\Resources\RegistrationQuestionResource\Pages;

use App\Filament\Coordinator\Resources\RegistrationQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRegistrationQuestions extends ManageRecords
{
    protected static string $resource = RegistrationQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
