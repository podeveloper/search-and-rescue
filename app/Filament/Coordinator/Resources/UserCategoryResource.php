<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Coordinator\Resources\UserCategoryResource\Pages;
use App\Filament\Coordinator\Resources\UserCategoryResource\RelationManagers;
use App\Models\UserCategory;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class UserCategoryResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = UserCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Members';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->string()
                    ->label(__('general.name')),
                Forms\Components\Select::make('users')
                    ->relationship('users', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('general.people')),
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
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->toggleable()
                    ->sortable()
                    ->label(__('general.user_category_count')),
            ])
            ->paginated([10, 25, 50])
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
                    //
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUserCategories::route('/'),
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
