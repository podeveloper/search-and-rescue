<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Resources\TodoCategoryResource\Pages;
use App\Filament\Resources\TodoCategoryResource\RelationManagers;
use App\Models\TodoCategory;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class TodoCategoryResource extends Resource
{
    protected static ?string $model = TodoCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Todo';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpan('full')
                            ->label(__('general.name'))
                            ->string(),
                        Forms\Components\ColorPicker::make('color')->required()
                            ->label(__('general.color')),
                        Forms\Components\TextInput::make('sort_order')->required()->numeric()->minValue(0)->maxValue(1000000)
                            ->label(__('general.sort_order')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.name')),
                Tables\Columns\ColorColumn::make('color')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.color')),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.sort_order')),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('sort_order')
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
            'index' => \App\Filament\Coordinator\Resources\TodoCategoryResource\Pages\ListTodoCategories::route('/'),
            'create' => \App\Filament\Coordinator\Resources\TodoCategoryResource\Pages\CreateTodoCategory::route('/create'),
            'edit' => \App\Filament\Coordinator\Resources\TodoCategoryResource\Pages\EditTodoCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = strtolower(str_replace(' ', '_', self::getModelLabel())) . '_count';

        return Cache::rememberForever($cacheKey, function () {
            return self::getModel()::count();
        });
    }

    public static function getNavigationLabel(): string
    {
        return __('todo_category.plural');
    }

    public static function getModelLabel(): string
    {
        return __('todo_category.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('todo_category.plural');
    }
}
