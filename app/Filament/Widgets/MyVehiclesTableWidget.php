<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use App\Models\VehicleModel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MyVehiclesTableWidget extends BaseWidget
{
    protected function getTableHeading(): string|Htmlable|null
    {
        return __('general.my_vehicles');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Vehicle::where('user_id', auth()->user()->id)
                    ->orderBy('id', 'desc')
            )
            ->columns([
                Split::make([
                    TextColumn::make('licence_plate')
                        ->weight(FontWeight::Bold)
                        ->sortable()
                        ->toggleable()
                        ->label(__('general.licence_plate')),
                    TextColumn::make('model.name')
                        ->weight(FontWeight::Bold)
                        ->sortable()
                        ->toggleable()
                        ->label(__('general.model')),
                ]),
                Panel::make([
                    Split::make([
                        TextColumn::make('category_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.category') . ':'),
                        TextColumn::make('category.name')
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                    Split::make([
                        TextColumn::make('brand_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.brand') . ':'),
                        TextColumn::make('brand.name')
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                    Split::make([
                        TextColumn::make('model_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.model') . ':'),
                        TextColumn::make('model.name')
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                    Split::make([
                        TextColumn::make('color_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.color') . ':'),
                        TextColumn::make('color')
                            ->formatStateUsing(fn($state)=>__('general.'.$state))
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                    Split::make([
                        TextColumn::make('licence_plate_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.licence_plate') . ':'),
                        TextColumn::make('licence_plate')
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                    Split::make([
                        TextColumn::make('year_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.year') . ':'),
                        TextColumn::make('year')
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                    Split::make([
                        TextColumn::make('mileage_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.mileage') . ':'),
                        TextColumn::make('mileage')
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                    Split::make([
                        TextColumn::make('vin_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.vin') . ':'),
                        TextColumn::make('vin')
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                ])
                    ->collapsed(false)
                    ->collapsible(),
            ])
            ->paginated([10, 25, 50])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('general.vehicle_create'))
                    ->label(__('general.vehicle_create'))
                    ->form([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->exists('vehicle_categories','id')
                            ->label(__('general.category')),
                        Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->exists('vehicle_brands','id')
                            ->label(__('general.brand')),
                        Select::make('model_id')
                            ->options(fn(Get $get): Collection => VehicleModel::query()
                                ->where('vehicle_category_id',$get('category_id'))
                                ->where('vehicle_brand_id',$get('brand_id'))
                                ->pluck('name','id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Model')
                            ->required()
                            ->exists('vehicle_models','id')
                            ->label(__('general.model')),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(1950)
                            ->maxValue(now()->year)
                            ->label(__('general.year')),
                        Select::make('color')
                            ->searchable()
                            ->options(Vehicle::colors())
                            ->required()
                            ->label(__('general.color')),
                        TextInput::make('licence_plate')
                            ->required()
                            ->maxLength(255)
                            ->label(__('general.licence_plate')),
                        TextInput::make('vin')
                            ->nullable()
                            ->maxLength(255)
                            ->label(__('general.vin')),
                        TextInput::make('mileage')
                            ->numeric()
                            ->label(__('general.mileage')),
                        Hidden::make('user_id')
                            ->default(fn()=>auth()->user()->id)
                            ->required(),
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->form([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->exists('vehicle_categories','id')
                            ->label(__('general.category')),
                        Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->exists('vehicle_brands','id')
                            ->label(__('general.brand')),
                        Select::make('model_id')
                            ->options(fn(Get $get): Collection => VehicleModel::query()
                                ->where('vehicle_category_id',$get('category_id'))
                                ->where('vehicle_brand_id',$get('brand_id'))
                                ->pluck('name','id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Model')
                            ->required()
                            ->exists('vehicle_models','id')
                            ->label(__('general.model')),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(1950)
                            ->maxValue(now()->year)
                            ->label(__('general.year')),
                        Select::make('color')
                            ->searchable()
                            ->options(Vehicle::colors())
                            ->required()
                            ->label(__('general.color')),
                        TextInput::make('licence_plate')
                            ->required()
                            ->maxLength(255)
                            ->label(__('general.licence_plate')),
                        TextInput::make('vin')
                            ->nullable()
                            ->maxLength(255)
                            ->label(__('general.vin')),
                        TextInput::make('mileage')
                            ->numeric()
                            ->label(__('general.mileage')),
                        Hidden::make('user_id')
                            ->default(fn()=>auth()->user()->id)
                            ->required(),
                    ]),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->modalHeading(__('general.vehicle_remove'))
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(Vehicle $record) => $record->delete()),
            ], position: ActionsPosition::BeforeColumns)
            ->emptyStateHeading(__('general.table_empty_state_heading'))
            ->emptyStateDescription('');
    }
}
