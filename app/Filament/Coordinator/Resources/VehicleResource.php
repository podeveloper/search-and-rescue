<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\VehicleResource\Pages;
use App\Filament\Coordinator\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->exists('vehicle_categories','id'),
                Forms\Components\Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->exists('vehicle_brands','id'),
                Forms\Components\Select::make('model_id')
                    ->options(fn(Forms\Get $get): Collection => VehicleModel::query()
                        ->where('vehicle_category_id',$get('category_id'))
                        ->where('vehicle_brand_id',$get('brand_id'))
                        ->pluck('name','id'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->label('Model')
                    ->required()
                    ->exists('vehicle_models','id'),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->minValue(1950)
                    ->maxValue(now()->year),
                Forms\Components\Select::make('color')
                    ->searchable()
                    ->options(Vehicle::colors())
                    ->required(),
                Forms\Components\TextInput::make('licence_plate')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('vin')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mileage')
                    ->numeric(),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('licence_plate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mileage')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ManageVehicles::route('/'),
        ];
    }
}
