<?php

namespace App\Filament\Coordinator\Resources\UserResource\RelationManagers;

use App\Filament\Coordinator\Resources\DriverLicenceResource;
use App\Models\DriverLicence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DriverLicencesRelationManager extends RelationManager
{
    protected static string $relationship = 'driverLicences';

    public function form(Form $form): Form
    {
        return DriverLicenceResource::form($form);
    }

    public function table(Table $table): Table
    {
        return DriverLicenceResource::table($table)
            ->recordTitleAttribute('class')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->icon('heroicon-o-document-text')
                    ->url(function (DriverLicence $record){
                        return $record->pdf ? route('files.show', ['path' => $record->pdf]) : '';
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (DriverLicence $record): string => $record->pdf != null),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
