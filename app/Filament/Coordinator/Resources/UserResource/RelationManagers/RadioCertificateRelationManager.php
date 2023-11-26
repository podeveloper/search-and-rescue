<?php

namespace App\Filament\Coordinator\Resources\UserResource\RelationManagers;

use App\Filament\Coordinator\Resources\RadioCertificateResource;
use App\Models\RadioCertificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RadioCertificateRelationManager extends RelationManager
{
    protected static string $relationship = 'radioCertificate';

    public function form(Form $form): Form
    {
        return RadioCertificateResource::form($form);
    }

    public function table(Table $table): Table
    {
        return RadioCertificateResource::table($table)
            ->recordTitleAttribute('call_sign')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->icon('heroicon-o-document-text')
                    ->url(function (RadioCertificate $record){
                        return $record->pdf ? route('files.show', ['path' => $record->pdf]) : '';
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (RadioCertificate $record): string => $record->pdf != null),
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
