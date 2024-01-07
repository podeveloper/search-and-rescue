<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\RegistrationQuestionAnswerResource\Pages;
use App\Filament\Coordinator\Resources\RegistrationQuestionAnswerResource\RelationManagers;
use App\Models\RegistrationQuestionAnswer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class RegistrationQuestionAnswerResource extends Resource
{
    protected static ?string $model = RegistrationQuestionAnswer::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRegistrationQuestionAnswers::route('/'),
        ];
    }
}
