<?php

namespace App\Filament\Coordinator\Resources\RegistrationQuestionAnswerResource\Pages;

use App\Filament\Coordinator\Resources\RegistrationQuestionAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRegistrationQuestionAnswers extends ManageRecords
{
    protected static string $resource = RegistrationQuestionAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
