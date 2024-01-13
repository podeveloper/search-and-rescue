<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
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

class MyEquipmentsTableWidget extends BaseWidget
{
    protected function getTableHeading(): string|Htmlable|null
    {
        return __('general.my_equipments');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Equipment::with('users')->whereHas('users', function ($query){
                    $query->where('user_id', auth()->user()->id);
                })
                    ->orderBy('name')
            )
            ->columns([
                Split::make([
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->label(__('general.name')),
                    TextColumn::make('brand')
                        ->default(fn(Equipment $equipment) => $equipment->users->first()->pivot->brand)
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable()
                        ->toggleable(),
                ]),
                Panel::make([
                    Split::make([
                        TextColumn::make('name_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.name') . ':'),
                        TextColumn::make('name')
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
                        TextColumn::make('brand')
                            ->default(fn(Equipment $equipment) => strtoupper($equipment->users->first()->pivot->brand))
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
                            ->default(fn(Equipment $equipment) => strtoupper($equipment->users->first()->pivot->color))
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                    Split::make([
                        TextColumn::make('size_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.body_size') . ':'),
                        TextColumn::make('size')
                            ->default(fn(Equipment $equipment) => strtoupper($equipment->users->first()->pivot->size))
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                    Split::make([
                        TextColumn::make('is_wearable_label')
                            ->weight(FontWeight::Bold)
                            ->default(fn()=> __('general.is_wearable') . ':'),
                        TextColumn::make('is_wearable')
                            ->formatStateUsing(fn(Equipment $equipment) => $equipment->is_wearable ? __('general.yes') : __('general.no'))
                            ->badge()
                            ->extraAttributes(['class' => 'mb-2'])
                            ->searchable()
                            ->sortable()
                            ->toggleable(),
                    ]),
                ])
                    ->collapsible(),
            ])
            ->paginated([10, 25, 50])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->action(function (array $data){
                        $equipment = Equipment::find($data["equipment_id"]);
                        $equipment->users()->attach(auth()->user()->id,[
                            'brand' => $data["brand"],
                            'color' => $data["color"],
                            'size' => $data["size"],
                        ]);
                    })
                    ->modalHeading(__('general.equipment_create'))
                    ->label(__('general.equipment_create'))
                    ->form(fn (Tables\Actions\Action $action): array => [
                        Select::make('equipment_id')
                            ->relationship('users')
                            ->label(__('general.equipment'))
                            ->options(Equipment::all()->pluck('name','id'))
                            ->preload()
                            ->searchable()
                            ->required(),
                        TextInput::make('brand')
                            ->label(__('general.brand'))
                            ->required(),
                        Select::make('color')
                            ->label(__('general.color'))
                            ->options(Equipment::colors())
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('size')
                            ->label(__('general.size'))
                            ->options(Equipment::sizes())
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->action(fn(Equipment $equipment)=>$equipment->users()->detach(auth()->user()->id))
                    ->button()
                    ->color('danger')
                    ->size(ActionSize::ExtraSmall)
                    ->modalHeading(__('general.equipment_remove'))
                    ->label(__('general.equipment_remove')),
            ], position: ActionsPosition::BeforeColumns);
    }
}
