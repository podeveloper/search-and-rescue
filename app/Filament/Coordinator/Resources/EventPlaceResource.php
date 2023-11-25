<?php

namespace App\Filament\Coordinator\Resources;

use App\Filament\Resources\EventPlaceResource\Pages;
use App\Filament\Resources\EventPlaceResource\RelationManagers;
use App\Models\EventPlace;
use App\Traits\NavigationLocalizationTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class EventPlaceResource extends Resource
{
    use NavigationLocalizationTrait;

    protected static ?string $model = EventPlace::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Events';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->string()
                    ->label(__('general.name')),
                Forms\Components\Select::make('type')
                    ->options([
                        'center' => 'Center',
                        'mosque' => 'Mosque',
                        'other' => 'Other',
                    ])
                    ->required()
                    ->string()
                    ->label(__('general.type')),
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
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.type')),
                Tables\Columns\TextColumn::make('events_count')
                    ->counts('events')
                    ->label('Event Count')
                    ->sortable()
                    ->toggleable()
                    ->label(__('event_place.count')),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort('type','asc')
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
            'index' => \App\Filament\Coordinator\Resources\EventPlaceResource\Pages\ManageEventPlaces::route('/'),
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
