<?php

namespace App\Filament\Reference\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class CityResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = City::class;

    protected static ?string $navigationGroup = 'Reference Models';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->string()
                    ->label(__('general.name')),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->string(),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('countries','id')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->label(__('general.name')),
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->label(__('general.country_singular')),
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

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Reference\Resources\CityResource\Pages\ManageCities::route('/'),
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
