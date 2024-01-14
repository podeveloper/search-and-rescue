<?php

namespace App\Filament\Reference\Resources;

use App\Filament\Resources\NationalityResource\Pages;
use App\Filament\Resources\NationalityResource\RelationManagers;
use App\Models\Nationality;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class NationalityResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = Nationality::class;

    protected static ?string $navigationGroup = 'Reference Models';

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->string()
                    ->label(__('general.name')),
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
            'index' => \App\Filament\Reference\Resources\NationalityResource\Pages\ManageNationalities::route('/'),
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
}
