<?php

namespace App\Filament\Coordinator\Resources\UserResource\RelationManagers;

use App\Filament\Coordinator\Resources\ForestFireFightingCertificateResource;
use App\Models\ForestFireFightingCertificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ForestFireFightingCertificateRelationManager extends RelationManager
{
    protected static string $relationship = 'forestFireFightingCertificate';

    public function form(Form $form): Form
    {
        return ForestFireFightingCertificateResource::form($form);
    }

    public function table(Table $table): Table
    {
        return ForestFireFightingCertificateResource::table($table)
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->icon('heroicon-o-document-text')
                    ->url(function (ForestFireFightingCertificate $record){
                        return $record->pdf ? route('files.show', ['path' => $record->pdf]) : '';
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (ForestFireFightingCertificate $record): string => $record->pdf != null),
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
