<?php

namespace App\Filament\Stock\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Filament\Resources\StockMovementResource\RelationManagers;
use App\Filament\Stock\Resources;
use App\Models\StockMovement;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class StockMovementResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Stock Operations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->default(now())
                    ->maxDate(now())
                    ->required()
                    ->label(__('general.date')),
                Forms\Components\Select::make('material_id')
                    ->createOptionForm(fn(Form $form) => MaterialResource::form($form))
                    ->editOptionForm(fn(Form $form) => MaterialResource::form($form))
                    ->relationship('material', 'name')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label(__('general.material_singular'))
                    ->exists('materials','id'),
                Forms\Components\Select::make('from_where')
                    ->createOptionForm(fn(Form $form) => StockPlaceResource::form($form))
                    ->editOptionForm(fn(Form $form) => StockPlaceResource::form($form))
                    ->relationship('fromWhere', 'name')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label(__('general.from_where'))
                    ->exists('stock_places','id'),
                Forms\Components\Select::make('to_where')
                    ->createOptionForm(fn(Form $form) => StockPlaceResource::form($form))
                    ->editOptionForm(fn(Form $form) => StockPlaceResource::form($form))
                    ->relationship('toWhere', 'name')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label(__('general.to_where'))
                    ->exists('stock_places','id'),
                Forms\Components\Select::make('user_id')
                    ->relationship('operator', 'name')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label(__('general.operator'))
                    ->exists('users','id'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->maxValue('20000')
                    ->label(__('general.amount')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.date')),
                Tables\Columns\TextColumn::make('material.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.material_singular')),
                Tables\Columns\TextColumn::make('fromWhere.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.from_where')),
                Tables\Columns\TextColumn::make('toWhere.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.to_where')),
                Tables\Columns\TextColumn::make('operator.full_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.operator')),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.amount')),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('date','desc')
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
            'index' => Resources\StockMovementResource\Pages\ManageStockMovements::route('/'),
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
