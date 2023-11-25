<?php

namespace App\Filament\Stock\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Filament\Resources\MaterialResource\RelationManagers;
use App\Filament\Stock\Resources;
use App\Models\Material;
use Archilex\ToggleIconColumn\Columns\ToggleIconColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Stock Operations';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name','author','translator','type','language.name','materialCategory.name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->string()
                    ->label(__('general.name')),
                Forms\Components\Select::make('material_category_id')
                    ->relationship('materialCategory', 'name')
                    ->createOptionForm(fn(Form $form) => MaterialCategoryResource::form($form))
                    ->editOptionForm(fn(Form $form) => MaterialCategoryResource::form($form))
                    ->preload()
                    ->searchable()
                    ->nullable()
                    ->exists('material_categories','id'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('materialCategory.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('name','asc')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Resources\MaterialResource\Pages\ListMaterials::route('/'),
            'create' => Resources\MaterialResource\Pages\CreateMaterial::route('/create'),
            'edit' => Resources\MaterialResource\Pages\EditMaterial::route('/{record}/edit'),
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
