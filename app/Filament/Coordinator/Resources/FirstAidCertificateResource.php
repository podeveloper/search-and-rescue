<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\FirstAidCertificateResource\Pages;
use App\Filament\Coordinator\Resources\FirstAidCertificateResource\RelationManagers;
use App\Models\FirstAidCertificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FirstAidCertificateResource extends Resource
{
    protected static ?string $model = FirstAidCertificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->numeric(),
                Forms\Components\TextInput::make('licence_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('training_institution')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date_of_issue'),
                Forms\Components\DatePicker::make('expiration_date'),
                Forms\Components\TextInput::make('pdf')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('licence_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('training_institution')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_issue')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pdf')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFirstAidCertificates::route('/'),
        ];
    }
}
