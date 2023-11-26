<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\RadioCertificateResource\Pages;
use App\Filament\Coordinator\Resources\RadioCertificateResource\RelationManagers;
use App\Models\RadioCertificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RadioCertificateResource extends Resource
{
    protected static ?string $model = RadioCertificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->numeric(),
                Forms\Components\TextInput::make('call_sign')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('licence_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('licence_class')
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
                Tables\Columns\TextColumn::make('call_sign')
                    ->searchable(),
                Tables\Columns\TextColumn::make('licence_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('licence_class')
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
            'index' => Pages\ManageRadioCertificates::route('/'),
        ];
    }
}
