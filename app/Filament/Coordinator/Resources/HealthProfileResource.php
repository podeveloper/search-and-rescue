<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\HealthProfileResource\Pages;
use App\Filament\Coordinator\Resources\HealthProfileResource\RelationManagers;
use App\Models\HealthProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HealthProfileResource extends Resource
{
    protected static ?string $model = HealthProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('blood_type')
                    ->columnSpanFull()
                    ->options(Healthprofile::bloodTypes()),
                Forms\Components\Textarea::make('medications')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('allergies')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('medical_conditions')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('vision_aids')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('prosthetics')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('emergency_contact_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('emergency_contact_phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Textarea::make('other_health_information')
                    ->columnSpanFull(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('blood_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('emergency_contact_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('emergency_contact_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ManageHealthProfiles::route('/'),
        ];
    }
}
