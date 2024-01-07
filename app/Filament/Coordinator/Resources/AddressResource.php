<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Resources\AddressResource\Pages;
use App\Filament\Resources\AddressResource\RelationManagers;
use App\Filament\Volunteer\Resources;
use App\Models\Address;
use App\Models\City;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class AddressResource extends Resource
{
    protected static ?string $model = Address::class;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'home' => 'Home',
                        'work' => 'Work',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->exists('countries','id'),
                Forms\Components\Select::make('city_id')
                    ->options(fn(Forms\Get $get): Collection => City::query()
                    ->where('country_id',$get('country_id'))
                    ->pluck('name','id'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('cities','id'),
                Forms\Components\Select::make('district_id')
                    ->options(fn(Forms\Get $get): Collection => District::query()
                        ->where('city_id',$get('city_id'))
                        ->pluck('name','id'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('districts','id'),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->exists('users','id'),
                Forms\Components\Textarea::make('full_address')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('district.name')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('full_address')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Coordinator\Resources\AddressResource\Pages\ListAddresses::route('/'),
            'create' => \App\Filament\Coordinator\Resources\AddressResource\Pages\CreateAddress::route('/create'),
            'edit' => \App\Filament\Coordinator\Resources\AddressResource\Pages\EditAddress::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = strtolower(str_replace(' ', '_', self::getModelLabel())) . '_count';

        return Cache::rememberForever($cacheKey, function () {
            return self::getModel()::count();
        });
    }
}
