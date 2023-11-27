<?php

namespace App\Filament\Coordinator\Resources\UserResource\RelationManagers;

use App\Filament\Coordinator\Resources\VehicleResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehiclesRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicles';

    public function form(Form $form): Form
    {
        return VehicleResource::form($form);
    }

    public function table(Table $table): Table
    {
        return VehicleResource::table($table)
            ->recordTitleAttribute('licence_plate')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
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
