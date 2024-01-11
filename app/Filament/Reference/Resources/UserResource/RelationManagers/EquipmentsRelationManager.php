<?php

namespace App\Filament\Reference\Resources\UserResource\RelationManagers;

use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'equipments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('brand'),
                Tables\Columns\TextColumn::make('color'),
                Tables\Columns\TextColumn::make('size'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('brand')->required(),
                        Forms\Components\Select::make('color')
                            ->options(Equipment::colors())
                            ->preload()
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('size')
                            ->options(Equipment::sizes())
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // ...
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
