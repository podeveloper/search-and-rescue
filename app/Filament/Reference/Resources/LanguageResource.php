<?php

namespace App\Filament\Reference\Resources;

use App\Filament\Resources\LanguageResource\Pages;
use App\Filament\Resources\LanguageResource\RelationManagers;
use App\Models\Language;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class LanguageResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = Language::class;

    protected static ?string $navigationGroup = 'Reference Models';
    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?int $navigationSort = 8;

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
                Forms\Components\TextInput::make('native_name')
                    ->required()
                    ->string(),
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
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.code')),
                Tables\Columns\TextColumn::make('native_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.native')),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.user_count')),
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
            'index' => \App\Filament\Reference\Resources\LanguageResource\Pages\ManageLanguages::route('/'),
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
