<?php

namespace App\Filament\Reference\Resources;

use App\Filament\Reference\Resources\EquipmentResource\Pages;
use App\Filament\Reference\Resources\EquipmentResource\RelationManagers;
use App\Models\Equipment;
use Archilex\ToggleIconColumn\Columns\ToggleIconColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    protected static ?string $navigationGroup = 'Reference Models';

    protected static ?int $navigationSort = 18;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->string()
                    ->label(__('general.name')),
                Forms\Components\Toggle::make('is_wearable')
                    ->onIcon('heroicon-m-bolt')
                    ->offIcon('heroicon-m-bolt')
                    ->inline(false)
                    ->label(__('general.is_wearable')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('general.name')),
                Tables\Columns\IconColumn::make('is_wearable')
                    ->icon(fn(Equipment $equipment)=> $equipment->is_wearable ? 'heroicon-m-user': 'heroicon-m-briefcase')
                    ->color(fn(Equipment $equipment)=> $equipment->is_wearable ? 'success': 'danger')
                    ->toggleable()
                    ->label(__('general.is_wearable')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEquipment::route('/'),
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
