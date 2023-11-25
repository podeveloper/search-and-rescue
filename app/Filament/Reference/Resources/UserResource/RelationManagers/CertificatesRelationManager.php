<?php

namespace App\Filament\Reference\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CertificatesRelationManager extends RelationManager
{
    protected static string $relationship = 'certificates';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('issue_date')
                    ->nullable(),
                Forms\Components\DatePicker::make('expiry_date')
                    ->nullable(),
                Forms\Components\TextInput::make('issuer')
                    ->nullable(),
                Forms\Components\TextInput::make('url')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('issue_date'),
                Tables\Columns\TextColumn::make('expiry_date'),
                Tables\Columns\TextColumn::make('issuer'),
                Tables\Columns\TextColumn::make('url'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\DatePicker::make('issue_date')
                            ->nullable(),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->nullable(),
                        Forms\Components\TextInput::make('issuer')
                            ->nullable(),
                        Forms\Components\TextInput::make('url')
                            ->nullable(),
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
