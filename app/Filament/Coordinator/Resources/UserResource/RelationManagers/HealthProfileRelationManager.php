<?php

namespace App\Filament\Coordinator\Resources\UserResource\RelationManagers;

use App\Filament\Coordinator\Resources\HealthProfileResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HealthProfileRelationManager extends RelationManager
{
    protected static string $relationship = 'healthProfile';

    public function form(Form $form): Form
    {
        return HealthProfileResource::form($form);
    }

    public function table(Table $table): Table
    {
        return HealthProfileResource::table($table)
            ->recordTitleAttribute('blood_type')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
