<?php

namespace App\Filament\Reference\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\CountryResource\RelationManagers;
use App\Models\Country;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class CountryResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = Country::class;

    protected static ?string $navigationGroup = 'Reference Models';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->string()
                    ->label(__('general.name')),
                Forms\Components\Select::make('continent_id')
                    ->relationship('continent', 'name')
                    ->nullable()
                    ->exists('continents', 'id')
                    ->label(__('general.continent')),
                Forms\Components\Select::make('region_id')
                    ->relationship('region', 'name')
                    ->nullable()
                    ->exists('regions', 'id')
                    ->label(__('general.region')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cities_count')
                    ->counts('cities')
                    ->toggleable()
                    ->label(__('general.city_count')),
                Tables\Columns\TextColumn::make('continent.name')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.continent')),
                Tables\Columns\TextColumn::make('region.name')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.region')),
                Tables\Columns\TextColumn::make('capital')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.capital')),
                Tables\Columns\TextColumn::make('native')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.native')),
                Tables\Columns\TextColumn::make('iso2')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('iso3')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone_code')
                    ->searchable()
                    ->toggleable()
                    ->label(__('general.phone_code')),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('name','asc')
            ->filters([
                //
            ])
            ->actions([
                //
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
            'index' => \App\Filament\Reference\Resources\CountryResource\Pages\ListCountries::route('/'),
            'create' => \App\Filament\Reference\Resources\CountryResource\Pages\CreateCountry::route('/create'),
            'edit' => \App\Filament\Reference\Resources\CountryResource\Pages\EditCountry::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = strtolower(str_replace(' ', '_', self::getModelLabel())) . '_count';

        return Cache::rememberForever($cacheKey, function () {
            return self::getModel()::count();
        });
    }

    public static function canDelete(Model $record) : bool
    {
        return auth()->user()->is_admin;
    }

    public static function canDeleteAny() : bool
    {
        return auth()->user()->is_admin;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
