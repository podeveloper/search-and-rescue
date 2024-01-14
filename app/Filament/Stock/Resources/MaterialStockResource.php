<?php

namespace App\Filament\Stock\Resources;

use App\Filament\Resources\MaterialStockResource\Pages;
use App\Filament\Resources\MaterialStockResource\RelationManagers;
use App\Filament\Stock\Resources;
use App\Models\MaterialStock;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class MaterialStockResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = MaterialStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationGroup = 'Stock Operations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('material_id')
                    ->relationship('material', 'name')
                    ->createOptionForm(fn(Form $form) => MaterialResource::form($form))
                    ->editOptionForm(fn(Form $form) => MaterialResource::form($form))
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label(__('general.name'))
                    ->exists('materials','id'),
                Forms\Components\Select::make('stock_place_id')
                    ->relationship('stockPlace', 'name')
                    ->createOptionForm(fn(Form $form) => StockPlaceResource::form($form))
                    ->editOptionForm(fn(Form $form) => StockPlaceResource::form($form))
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label(__('general.stock_place_singular'))
                    ->exists('stock_places','id'),
                Forms\Components\TextInput::make('lower_limit')
                    ->required()
                    ->numeric()
                    ->label(__('general.lower_limit')),
                Forms\Components\TextInput::make('current_amount')
                    ->required()
                    ->numeric()
                    ->label(__('general.current_amount')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('material.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.name')),
                Tables\Columns\TextColumn::make('material.materialCategory.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.material_category_singular')),
                Tables\Columns\TextColumn::make('stockPlace.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.stock_place_singular')),
                Tables\Columns\TextColumn::make('lower_limit')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.lower_limit')),
                Tables\Columns\TextColumn::make('current_amount')
                    ->sortable()
                    ->toggleable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                    ])
                    ->label(__('general.current_amount')),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('stock_place_id','asc')
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Resources\MaterialStockResource\Pages\ManageMaterialStocks::route('/'),
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
